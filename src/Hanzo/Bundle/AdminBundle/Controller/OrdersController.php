<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Propel;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersAttributesQuery;
use Hanzo\Model\OrdersSyncLogQuery;
use Hanzo\Model\DomainsQuery;

class OrdersController extends CoreController
{

    public function indexAction($customer_id, $domain_key, $pager)
    {

        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
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

        if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
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
                ->withColumn('SUM(orders_lines.price)','TotalPrice')
                ->groupByOrdersId()
                ->findOne($this->getDbConnection())
            ;

            if ($orders_count instanceof OrdersLines) {
                $order_data[] = array(
                    'id' => $order->getId(),
                    'createdat' => $order->getCreatedAt(),
                    'finishedat' => $order->getFinishedAt(),
                    'totallines' => $orders_count->getVirtualColumn('TotalLines'),
                    'totalprice' => $orders_count->getVirtualColumn('TotalPrice')
                );
            }else{
                $order_data[] = array(
                    'id' => $order->getId(),
                    'createdat' => $order->getCreatedAt(),
                    'finishedat' => $order->getFinishedAt(),
                    'totallines' => '0',
                    'totalprice' => '0,00'
                );
            }

        }

        $paginate = null;
        if ($orders->haveToPaginate()) {

            $pages = array();
            foreach ($orders->getLinks(20) as $page) {
                $pages[$page] = $router->generate($route, array('customer_id' => $customer_id, 'pager' => $page), TRUE);

            }

            $paginate = array(
                'next' => ($orders->getNextPage() == $pager ? '' : $router->generate($route, array('customer_id' => $customer_id, 'pager' => $orders->getNextPage()), TRUE)),
                'prew' => ($orders->getPreviousPage() == $pager ? '' : $router->generate($route, array('customer_id' => $customer_id, 'pager' => $orders->getPreviousPage()), TRUE)),

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
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function viewAction($order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $order = OrdersQuery::create()
            ->findOneById($order_id, $this->getDbConnection())
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

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form_state->bindRequest($request);

            if ($form_state->isValid()) {

                $form_data = $form_state->getData();

                $order->setState($form_data['state']);
                $order->save($this->getDbConnection());

                $this->get('session')->setFlash('notice', 'admin.orders.state_log.inserted');
            }
        }

        return $this->render('AdminBundle:Orders:view.html.twig', array(
            'order'  => $order,
            'order_lines' => $order_lines,
            'order_attributes' => $order_attributes,
            'form_state' => $form_state->createView(),
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }



    public function previewAction($order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()->findOneById($order_id);
        if (!$order instanceof Orders) {
            if ('json' === $this->getFormat()) {
                return $this->json_response(array(
                    'status' => false,
                    'message' => 'Der findes ingen ordre med ID #' . $order_id
                ));
            }

            return $this->response('Der findes ingen ordre med ID #' . $order_id);
        }

        $debtor = $this->get('ax_manager')->sendDebtor($order->getCustomers(), true);
        $order = $this->get('ax_manager')->sendOrder($order, true);

        if ('json' === $this->getFormat()) {
            $html = '<h2>Debtor:</h2><pre>'.print_r($debtor, 1).'</pre><h2>Order:</h2><pre>'.print_r($order,1).'</pre>';
            return $this->json_response(array(
                'status' => true,
                'message' => '',
                'data' => array('html' => $html)
            ));
        }
    }

    public function syncStatusAction($status = 'failed')
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $orders = OrdersSyncLogQuery::create()
            ->filterByState('failed')
            ->find();

        return $this->render('AdminBundle:Orders:failed_orders_list.html.twig', array(
            'orders'  => $orders,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }


    public function resyncAction($order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()
            ->filterById($order_id)
            ->findOne()
        ;

        if (!$order instanceof Orders) {
            if ('json' === $this->getFormat()) {
                return $this->json_response(array(
                    'status' => false,
                    'message' => 'Der findes ingen ordre med ID #' . $order_id
                ));
            }

            return $this->response('Der findes ingen ordre med ID #' . $order_id);
        }

        // delete old log entry
        OrdersSyncLogQuery::create()
            ->filterByState('failed')
            ->filterByOrdersId($order_id)
            ->delete();

        $status = $this->get('ax_manager')->sendOrder($order, false);
        $message = $status ?
            'Ordren #%d er nu sendt' :
            'Ordren #%d kunne ikke gensendes !'
        ;

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => false,
                'message' => sprintf($message, $order_id),
            ));
        }

        return $this->response('Der findes ingen ordre med ID #' . $order_id);

    }

    /**
     * deleteOrder
     * @return void
     **/
    public function deleteAction($order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()->findOneById($order_id);
        if ($order) {
            $order->setIgnoreDeleteConstraints(true);
            $order->delete();
        }

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => true,
                'message' => 'Ordren blev slettet!',
            ));
        }
    }

    public function changeStateAction()
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
                    'choices' => Orders::$state_message_map,
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
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form_state->bindRequest($request);

            if ($form_state->isValid()) {

                $form_data = $form_state->getData();
                $orders = OrdersQuery::create()
                    ->where('orders.id >= ?', $form_data['id-from'])
                    ->where('orders.id <= ?', $form_data['id-to'])
                ;
                if($form_data['state-from'] != '')
                    $orders = $orders->filterByState($form_data['state-from']);

                $orders = $orders->find($this->getDbConnection());

                foreach ($orders as $order) {
                    $order->setState($form_data['state-to']);
                    $order->save($this->getDbConnection());
                }

                $this->get('session')->setFlash('notice', 'admin.orders.state_log.changed');
            }
        }
        return $this->render('AdminBundle:Orders:change_state.html.twig', array(
          'form' => $form_state->createView(),
          'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    /**
     * viewFailedAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function viewDeadAction()
    {
        $deadOrderBuster = $this->get('deadorder_manager');
        $orders = $deadOrderBuster->getOrders(null);

        foreach ($orders as $order) {
            $order->statemessage = Orders::$state_message_map[$order->getState()];
        }

        return $this->render('AdminBundle:Orders:dead_orders_list.html.twig', array(
              'orders' => $orders,
              'database' => $this->getRequest()->getSession()->get('database')
            ));
    }

    /**
     * checkDeadOrderAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function checkDeadOrderAction( $id )
    {
        $deadOrderBuster = $this->get('deadorder_manager');
        $order = OrdersQuery::create()->findPK($id);
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
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function performDeadAction( $action )
    {
        return $this->json_response( array('hest'=>true) );
    }
}
