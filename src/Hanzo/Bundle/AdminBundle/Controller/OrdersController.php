<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;

use Hanzo\Model\OrdersAttributes;
use Hanzo\Model\OrdersStateLog;
use Hanzo\Model\OrdersVersions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Propel;
use Exception;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersAttributesQuery;
use Hanzo\Model\OrdersDeletedLogQuery;
use Hanzo\Model\OrdersDeletedLog;
use Hanzo\Model\OrdersSyncLogQuery;
use Hanzo\Model\OrdersSyncLog;
use Hanzo\Model\DomainsQuery;

use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApi;
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApiCallException;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

class OrdersController extends CoreController
{

    public function indexAction(Request $request, $customer_id, $domain_key, $pager)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_LOGISTICS")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $request->get('_route');
        $router = $container->get('router');

        $orders = OrdersQuery::create();

        if(null != $customer_id)
            $orders = $orders->filterByCustomersId($customer_id);

        if(null != $domain_key){
            $orders = $orders
                ->useOrdersAttributesQuery()
                    ->filterByCKey('domain_key')
                    ->filterByCValue($domain_key)
                ->endUse()
                ->joinOrdersAttributes()
            ;
        }

        if (isset($_GET['debitor'])) {
            $debitor = $request->query->get('debitor', null);

            $orders = $orders->filterByCustomersId($debitor);
        }
        if (isset($_GET['q'])) {
            $q_clean = $request->query->get('q', null);
            $q = '%'.$q_clean.'%';
            $orders = $orders->filterByCustomersId($q_clean)
                ->_or()
                ->filterById($q_clean)
                ->_or()
                ->filterByEmail($q)
            ;
        }

        $orders = $orders->orderByCreatedAt('DESC')
            ->paginate($pager, 20, $this->getDbConnection())
        ;

        $order_data = array();

        foreach ($orders as $order) {

            $orders_count = OrdersLinesQuery::create()
                ->filterByOrdersId($order->getId())
                ->withColumn('SUM(orders_lines.quantity)','TotalLines')
                ->withColumn('SUM(orders_lines.price * orders_lines.quantity)','TotalPrice')
                ->groupByOrdersId()
                ->findOne($this->getDbConnection())
            ;

            $order_state = OrdersSyncLogQuery::create()
                ->orderByCreatedAt('DESC')
                ->filterByOrdersId($order->getId())
                ->findOne($this->getDbConnection())
            ;
            $state = '';
            if($order_state instanceof OrdersSyncLog){
                $state = $order_state->getState();
            }

            if ($orders_count instanceof OrdersLines) {
                $order_data[] = array(
                    'id' => $order->getId(),
                    'createdat'  => $order->getCreatedAt(),
                    'finishedat' => $order->getFinishedAt(),
                    'totallines' => $orders_count->getVirtualColumn('TotalLines'),
                    'totalprice' => $orders_count->getVirtualColumn('TotalPrice'),
                    'state'      => $state
                );
            }else{
                $order_data[] = array(
                    'id' => $order->getId(),
                    'createdat'  => $order->getCreatedAt(),
                    'finishedat' => $order->getFinishedAt(),
                    'totallines' => '0',
                    'totalprice' => '0,00',
                    'state'      => $state
                );
            }

        }

        $paginate = null;
        if ($orders->haveToPaginate()) {

            $pages = array();
            foreach ($orders->getLinks(20) as $page) {
                $pages[$page] = $router->generate($route, array('customer_id' => $customer_id, 'domain_key' => $domain_key, 'pager' => $page), TRUE);
            }

            $paginate = array(
                'next' => ($orders->getNextPage() == $pager ? '' : $router->generate($route, array('customer_id' => $customer_id, 'domain_key' => $domain_key, 'pager' => $orders->getNextPage()), TRUE)),
                'prew' => ($orders->getPreviousPage() == $pager ? '' : $router->generate($route, array('customer_id' => $customer_id, 'domain_key' => $domain_key, 'pager' => $orders->getPreviousPage()), TRUE)),

                'pages' => $pages,
                'index' => $pager
            );
        }

        $domains_availible = DomainsQuery::Create()->find($this->getDbConnection());

        return $this->render('AdminBundle:Orders:list.html.twig', array(
            'orders'  => $order_data,
            'paginate' => $paginate,
            'domains_availible' => $domains_availible,
            'domain_key' => $domain_key,
            'database' => $request->getSession()->get('database')
        ));
    }

    public function viewAction(Request $request, $order_id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_LOGISTICS")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $hanzo = Hanzo::getInstance();

        $order = OrdersQuery::create()
            ->filterById($order_id)
            ->findOne($this->getDbConnection())
        ;

        $order_lines = OrdersLinesQuery::create()
            ->filterByOrdersId($order_id)
            ->orderByProductsSku()
            ->find($this->getDbConnection())
        ;

        $order_attributes = OrdersAttributesQuery::create()
            ->filterByOrdersId($order_id)
            ->orderByNs()
            ->orderByCKey()
            ->joinWithOrders()
            ->find($this->getDbConnection())
        ;

        $attributes = array();
        $attachments = array();
        foreach ($order_attributes as $attribute) {
            if ('attachment' == $attribute->getNs()) {
                $o = $attribute->getOrders();
                $folder = $this->mapLanguageToPdfDir($o->getLanguagesId()).'_'.$o->getCreatedAt('Y');
                $attachments[] = [
                    'key'  => $attribute->getCKey(),
                    'file' => $attribute->getCValue(),
                    'path' => $hanzo->get('core.cdn') . 'pdf.php?' . http_build_query(array(
                        'folder' => $folder,
                        'file'   => $attribute->getCValue(),
                        'key'    => md5(time())
                    ))
                ];
            } else {
                $attributes[] = $attribute;
            }
        }

        $order_sync_states = OrdersSyncLogQuery::create()
            ->orderByCreatedAt('ASC')
            ->filterByOrdersId($order->getId())
            ->find($this->getDbConnection())
        ;
        $form_state = $this->createFormBuilder(array('state' => $order->getState()))
            ->add('state', 'choice',
                array(
                    'choices' => Orders::$state_message_map,
                    'label' => 'admin.orders.state_log.state',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form_state->bind($request);

            if ($form_state->isValid()) {

                $form_data = $form_state->getData();

                $order->setState($form_data['state']);
                $order->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'admin.orders.state_log.inserted');
            }
        }

        return $this->render('AdminBundle:Orders:view.html.twig', array(
            'order'  => $order,
            'order_lines' => $order_lines,
            'order_attributes' => $attributes,
            'order_attachments' => $attachments,
            'order_sync_states' => $order_sync_states,
            'form_state' => $form_state->createView(),
            'database' => $request->getSession()->get('database')
        ));
    }



    public function previewAction($order_id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_LOGISTICS")'))) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()->findOneById($order_id, $this->getDbConnection());
        if (!$order instanceof Orders) {
            if ('json' === $this->getFormat()) {
                return $this->json_response(array(
                    'status' => false,
                    'message' => 'Der findes ingen ordre med ID #' . $order_id
                ));
            }

            return $this->response('Der findes ingen ordre med ID #' . $order_id);
        }

        $debtor = $this->get('ax.out')->sendDebtor($order->getCustomers($this->getDbConnection()), true, $this->getDbConnection());
        $order = $this->get('ax.out')->sendOrder($order, true, $this->getDbConnection());

        if ('json' === $this->getFormat()) {
            $html = '<h2>Debtor:</h2><pre>'.print_r($debtor, 1).'</pre><h2>Order:</h2><pre>'.print_r($order,1).'</pre>';
            return $this->json_response(array(
                'status' => true,
                'message' => '',
                'data' => array('html' => $html)
            ));
        }
    }

    public function syncStatusAction(Request $request, $status = 'failed')
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $orders = OrdersSyncLogQuery::create()
            ->filterByState('failed')
            ->find($this->getDbConnection())
        ;

        foreach ($orders as &$order) {
            $order->data = unserialize($order->getContent());
        }

        return $this->render('AdminBundle:Orders:failed_orders_list.html.twig', array(
            'orders'  => $orders,
            'database' => $request->getSession()->get('database')
        ));
    }

    public function resyncAction($order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()->findOneById($order_id, $this->getDbConnection());

        if (!$order instanceof Orders) {
            if ('json' === $this->getFormat()) {
                return $this->json_response(array(
                    'status' => false,
                    'message' => 'Der findes ingen ordre med ID #' . $order_id
                ));
            }

            return $this->response('Der findes ingen ordre med ID #' . $order_id);
        }

        // find old log entry
        $order_log = OrdersSyncLogQuery::create()
            ->filterByState('failed')
            ->filterByOrdersId($order_id)
            ->findOne($this->getDbConnection())
        ;

        if ($order_log) {
            $log_data = unserialize($order_log->getContent());
            $order_log->delete($this->getDbConnection());
        }

        try {
            $this->get('ax.out')->sendOrder($order, false, $this->getDbConnection());
        } catch (Exception $e) {
            if ('json' === $this->getFormat()) {
                return $this->json_response(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }

            return false;
        }

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => true,
                'message' => sprintf('Ordren #%d er nu sendt', $order_id),
            ));
        }

        return true;
    }


    /**
     * @Template()
     */
    public function bulkSendOrdersAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('range', 'text')
            ->getForm()
        ;

        $status = [];
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            $values = $form->getData();

            list($min, $max) = explode('-', $values['range']);

            $orders = OrdersQuery::create()
                ->filterById(trim($min), \Criteria::GREATER_EQUAL)
                ->filterById(trim($max), \Criteria::LESS_EQUAL)
                ->find($this->getDbConnection())
            ;

            foreach ($orders as $order) {
                $status[$order->getId()] = $this->resyncAction($order->getId()) ? 'OK' : 'FAILED';
            }

        }

        return [
            'form'   => $form->createView(),
            'status' => $status,
            'database' => $request->getSession()->get('database')
        ];
    }


    /**
     * deleteOrder
     *
     * @param Request $request
     * @param int     $order_id
     * @return Response
     * @throws AccessDeniedException
     */
    public function deleteAction(Request $request, $order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()
            ->filterById($order_id)
            ->findOne($this->getDbConnection())
        ;

        if ($order) {
            // find old log entry and delete it
            $order_log = OrdersSyncLogQuery::create()
                ->filterByState('failed')
                ->filterByOrdersId($order_id)
                ->delete($this->getDbConnection())
            ;
            $order->setIgnoreDeleteConstraints(true);
            try {
                $order->delete($this->getDbConnection());
            } catch (Exception $e) {
                return $this->json_response(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => true,
                'message' => 'Ordren blev slettet!',
            ));
        }
    }

    public function changeStateAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }
        $form_state = $this->createFormBuilder()
            ->add('state-from', 'choice',
                array(
                    'choices' => Orders::$state_message_map,
                    'label' => 'admin.orders.state_from.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('state-to', 'choice',
                array(
                    'choices' => [
                        Orders::STATE_PENDING         => 'Order pending',
                        Orders::STATE_BEING_PROCESSED => 'Order beeing processed',
                        Orders::STATE_SHIPPED         => 'Order shipped/done',
                    ],
                    'label' => 'admin.orders.state_to.label',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('id-from', 'integer',
                array(
                    'label' => 'admin.orders.id_from.label',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('id-to', 'integer',
                array(
                    'label' => 'admin.orders.id_to.label',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('id-exclude', 'text',
                array(
                    'label' => 'admin.orders.id_exclude.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;

        $updated_orders = array();
        if ('POST' === $request->getMethod()) {
            $form_state->bind($request);

            if ($form_state->isValid()) {

                $form_data = $form_state->getData();
                $excluded_ids = array();
                if(!empty($form_data['id-exclude'])){
                    $excluded_ids = explode(",",trim($form_data['id-exclude'],','));
                }
                $orders = OrdersQuery::create()
                    ->where('orders.id >= ?', $form_data['id-from'])
                    ->where('orders.id <= ?', $form_data['id-to'])
                    ->where('orders.id NOT IN ?', $excluded_ids)
                ;
                if($form_data['state-from'] != '')
                    $orders = $orders->filterByState($form_data['state-from']);

                $orders = $orders->find($this->getDbConnection());
                foreach ($orders as $order) {
                    $order->setState($form_data['state-to']);
                    $order->save($this->getDbConnection());
                    $updated_orders[] = $order->getId();
                }

                $this->get('session')->getFlashBag()->add('notice', 'admin.orders.state_log.changed');
            }
        }
        return $this->render('AdminBundle:Orders:change_state.html.twig', array(
          'form' => $form_state->createView(),
          'updated_orders' => $updated_orders,
          'database' => $request->getSession()->get('database')
        ));
    }

    /**
     * viewFailedAction
     *
     * @param Request $request
     * @return Response
     */
    public function viewDeadAction(Request $request)
    {
        $deadOrderBuster = $this->get('deadorder_manager');
        $orders = $deadOrderBuster->getOrders(null);

        foreach ($orders as $order) {
            $order->statemessage = Orders::$state_message_map[$order->getState()];
        }

        return $this->render('AdminBundle:Orders:dead_orders_list.html.twig', array(
              'orders' => $orders,
              'database' => $request->getSession()->get('database')
            ));
    }

    /**
     * checkDeadOrderAction
     *
     * @param int $id
     * @return Response
     */
    public function checkDeadOrderAction( $id )
    {
        $deadOrderBuster = $this->get('deadorder_manager');
        $order = OrdersQuery::create()->findPK($id, $this->getDbConnection());
        $status = $deadOrderBuster->checkOrderForErrors($order);

        if ( $status['is_error'] )
        {
            return $this->json_response(array(
                'status' => false,
                'data'   => $status
                )
            );
        }
        return $this->json_response(array(
            'status' => true,
            'data'   => $status
            )
        );
    }

    /**
     * performDeadAction
     *
     * @param string $action
     * @return Response
     */
    public function performDeadAction( $action )
    {
        return $this->json_response( array('hest'=>true) );
    }

    /**
     * gothiaAction
     *
     * @param Request $request
     * @return Response
     */
    public function gothiaAction(Request $request)
    {
        return $this->render('AdminBundle:Orders:gothia.html.twig', [
            'database' => $request->getSession()->get('database')
        ] );
    }

    /**
     * gothiaGetOrderAction
     *
     * @param Request $request
     * @return Response
     */
    public function gothiaGetOrderAction(Request $request)
    {
        $return = array(
            'status'  => false,
            'message' => '',
            'data'    => array(),
        );

        $id = $request->request->get('order-id');

        $order = OrdersQuery::create()->findPK($id, $this->getDbConnection());

        if (!($order instanceOf Orders)) {
            $return['message'] = 'Ingen ordre med id "'.$id.'" fundet';
        } else {
            if ($order->getBillingMethod() != 'gothia') {
                $return['message'] = 'Der er ikke blevet brugt Gothia som betaling på ordre id "'.$id.'"';
            } else {
                $customer = $order->getCustomers($this->getDbConnection());

                $return['status'] = true;
                $return['message'] = 'Ok';
                $return['data']['order'] = array(
                    'id'       => $id,
                    'customer' => array(
                        'name' => $customer->getFirstName().' '. $customer->getLastName(),
                    ),
                    'amount'   => $order->getTotalPrice(),
                    'state'    => $order->getState(),
                );
            }
        }

        return $this->json_response( $return );
    }

    /**
     * gothiaPlaceReservationAction
     *
     * @param Request $request
     * @return Response
     */
    public function gothiaPlaceReservationAction(Request $request)
    {
        $api        = $this->get('payment.gothiaapi');
        $id         = $request->request->get('order-id');
        $order      = OrdersQuery::create()->findPK($id, $this->getDbConnection());
        $customer   = $order->getCustomers($this->getDbConnection());
        $translator = $this->get('translator');

        try
        {
            // Validate information @ gothia
            $api = $this->get('payment.gothiaapi');
            $response = $api->call()->checkCustomer($customer, $order);
        }
        catch( GothiaApiCallException $g )
        {
            Tools::debug( $g->getMessage(), __METHOD__);
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.checkcustomer.failed', array('%msg%' => $g->getMessage()), 'gothia'),
            ));
        }

        if ( $response->isError() )
        {
            if ( $response->data['PurchaseStop'] === 'true')
            {
                Tools::debug( 'PurchaseStop', __METHOD__, array( 'Transaction id' => $response->transactionId ));

                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.checkcustomer.purchasestop', array(), 'gothia'),
                ));
            }

            Tools::debug( 'Check customer error', __METHOD__, array( 'Transaction id' => $response->transactionId, 'Data' => $response->data ));

            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.checkcustomer.error', array(), 'gothia'),
            ));
        }

        try
        {
            $response = $api->call()->placeReservation( $customer, $order );
        }
        catch( GothiaApiCallException $g )
        {
            Tools::debug( $g->getMessage(), __METHOD__);

            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.placereservation.failed', array('%msg%' => $g->getMessage()), 'gothia'),
            ));
        }

        if ( $response->isError() )
        {
            Tools::debug( 'Confirm action error', __METHOD__, array( 'Transaction id' => $response->transactionId, 'Data' => $response->data ));

            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.placereservation.error', array(), 'gothia'),
            ));
        }

        return $this->json_response(array(
            'status' => true,
            'message' => 'Ok',
        ));
    }

    /**
     * gothiaCancelReservationAction
     *
     * @param Request $request
     * @return Response
     */
    public function gothiaCancelReservationAction(Request $request)
    {
        $api        = $this->get('payment.gothiaapi');
        $id         = $request->request->get('order-id');
        $order      = OrdersQuery::create()->findPK($id, $this->getDbConnection());
        $customer   = $order->getCustomers($this->getDbConnection());
        $translator = $this->get('translator');

        try
        {
            $response = $api->call()->cancelReservation( $customer, $order );
        }
        catch( GothiaApiCallException $g )
        {
            Tools::debug( $g->getMessage(), __METHOD__);

            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.cancelreservation.failed', array('%msg%' => $g->getMessage()), 'gothia'),
            ));
        }

        if ( $response->isError() )
        {
            Tools::debug( 'Cancel reservation error', __METHOD__, array( 'Transaction id' => $response->transactionId, 'Data' => $response->data ));

            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.cancelreservation.error', array(), 'gothia'),
            ));
        }

        return $this->json_response(array(
            'status' => true,
            'message' => 'Ok',
        ));
    }

    public function deletedOrdersAction(Request $request, $id = null)
    {
        $template_data = [
            'database' => $request->getSession()->get('database')
        ];

        if ($request->query->has('bulk')) {
            $form = $this->createFormBuilder()
                ->add('ids', 'textarea', [
                    'attr' => [
                        'cols' => 30,
                        'rows' => 20,
                    ]
                ])
                ->getForm()
            ;
            $template_data['form'] = $form->createView();
        }

        if ($request->query->has('q')) {
            $id = $request->query->get('q');
        }

        if ($id) {

            $order = OrdersDeletedLogQuery::create()->findOneByOrdersId($id, $this->getDbConnection());

            if (!$order instanceof OrdersDeletedLog) {
                $this->get('session')->getFlashBag()->add('notice', 'Der findes ingen slettede ordre med id #'.$id);
                return $this->redirect($this->generateUrl('admin_orders_deleted_order'));
            }

            $data = unserialize($order->getContent());
            $cdn = Hanzo::getInstance()->get('core.cdn');

            if (empty($data['orders'])) {
                $data['orders'] = $data['ordes'];
                unset ($data['ordes']);
            }

            $attributes = array();
            $attachments = array();
            foreach ($data['orders_attributes'] as $attribute) {
                if ('attachment' == $attribute['Ns']) {
                    $folder = $this->mapLanguageToPdfDir($data['orders']['LanguagesId']).'_'.substr($data['orders']['CreatedAt'], 0, 4);
                    $attachments[] = [
                        'key'  => $attribute['CKey'],
                        'file' => $attribute['CValue'],
                        'path' => $cdn . 'pdf.php?' . http_build_query(array(
                                'folder' => $folder,
                                'file'   => $attribute['CValue'],
                                'key'    => md5(time())
                            ))
                    ];
                } else {
                    $attributes[] = $attribute;
                }
            }

            $template_data['order']             = $data['orders'];
            $template_data['order_lines']       = $data['orders_lines'];
            $template_data['order_attributes']  = $attributes;
            $template_data['order_attachments'] = $attachments;
        }

        return $this->render('AdminBundle:Orders:deleted_order_view.html.twig', $template_data);
    }

    public function recreatedDeletedOrdersAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->redirect($this->generateUrl('admin_orders_deleted_order'));
        }

        $ids = explode("\n", str_replace("\r", '', $request->request->get('form[ids]', null, true)));

        if (empty($ids)) {
            return $this->redirect($this->generateUrl('admin_orders_deleted_order'));
        }

        $orders = OrdersDeletedLogQuery::create()
            ->filterByOrdersId($ids)
            ->find($this->getDbConnection())
        ;

        /** @var OrdersDeletedLog $record */
        foreach ($orders as $record) {
            $data = unserialize($record->getContent());
            if (empty($data['orders'])) {
                $data['orders'] = $data['ordes'];
                unset ($data['ordes']);
            }

            $order = new Orders();
            $order->fromArray($data['orders']);
            $order->setState(Orders::STATE_SHIPPED);
            $order->setInEdit(false);
            $order->setSessionId($order->getId());
            $order->save($this->getDbConnection());

            // set product lines
            $collection = new \PropelCollection();
            foreach ($data['orders_lines'] as $item) {
                $line = new OrdersLines();
                $line->fromArray($item);
                $collection->prepend($line);
            }
            $order->setOrdersLiness($collection);

            // ???
            OrdersAttributesQuery::create()
                ->findByOrdersId($order->getId(), $this->getDbConnection())
                ->delete($this->getDbConnection())
            ;
            $order->clearOrdersAttributess();

            // attributes
            $collection = new \PropelCollection();
            foreach ($data['orders_attributes'] as $item) {
                $line = new OrdersAttributes();
                $line->fromArray($item);
                $collection->prepend($line);
            }
            $order->setOrdersAttributess($collection);

            // state log
            $collection = new \PropelCollection();
            foreach ($data['orders_state_log'] as $item) {
                $line = new OrdersStateLog();
                $line->fromArray($item);
                $collection->prepend($line);
            }
            $order->setOrdersStateLogs($collection);

            // versions
            $collection = new \PropelCollection();
            foreach ($data['orders_versions'] as $item) {
                $line = new OrdersVersions();
                $line->fromArray($item);
                $collection->prepend($line);
            }
            $order->setOrdersVersionss($collection);

            $order->save($this->getDbConnection());
            $record->delete($this->getDbConnection());
        }

        $this->container->get('session')->getFlashBag()->add('notice', 'Ordren(e) er nu genoprettet.');
        return $this->redirect($this->generateUrl('admin_orders_deleted_order'));
    }

    /**
     * Resend an orders confirmation email.
     *
     * @param  Request $request
     * @param  int     $id
     * @return Response
     */
    public function resendConfirmationMailAction(Request $request, $id = null)
    {
        $order = OrdersQuery::create()->findOneById($id, $this->getDbConnection());

        if ($order->getState() < Orders::STATE_PENDING) {
            $this->container->get('session')->getFlashBag()->add('notice', 'Ordre nummer #'.$order->getId().' er ikke så langt at der kan gensendes en konfirmationsmail.');
            return $this->redirect($this->generateUrl('admin_customer_order', ['order_id' => $order->getId()]));
        }

        $mailer = $this->container->get('hanzo.order.send.confirmation.mail');
        $mailer->setDBConnection($this->getDbConnection());
        $mailer->build($order);
        $mailer->send();

        $this->container->get('session')->getFlashBag()->add('notice', 'Konfirmationsmailen er nu gensendt for ordre nummer #'.$order->getId().' til '.$order->getEmail());
        return $this->redirect($this->generateUrl('admin_customer_order', ['order_id' => $order->getId()]));
    }

    /**
     * @param int $orders_id
     * @param string state
     *
     * @return Response
     */
    public function deleteSyncLogMessageAction($orders_id, $state)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        OrdersSyncLogQuery::create()
            ->filterByOrdersId($orders_id)
            ->filterByState($state)
            ->delete($this->getDbConnection())
        ;

        return $this->json_response([
            'status'  => true,
            'message' => 'ok',
        ]);
    }
}
