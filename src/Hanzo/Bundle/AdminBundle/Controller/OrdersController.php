<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersAttributesQuery;
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

        $orders = $orders->orderByCreatedAt()
            ->orderById()
            ->paginate($pager, 20)
        ;

        $order_data = array();

        foreach ($orders as $order) {

            $orders_count = OrdersLinesQuery::create()
                ->filterByOrdersId($order->getId())
                ->withColumn('SUM(orders_lines.quantity)','TotalLines')
                ->withColumn('SUM(orders_lines.price)','TotalPrice')
                ->groupByOrdersId()
                ->findOne()
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
        
        $domains_availible = DomainsQuery::Create()->find();

        return $this->render('AdminBundle:Orders:list.html.twig', array(
            'orders'  => $order_data,
            'paginate' => $paginate,
            'domains_availible' => $domains_availible
        ));
    }

    public function viewAction($order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }
        
        $order = OrdersQuery::create()
            ->findOneById($order_id)
        ;

        $order_lines = OrdersLinesQuery::create()
            ->filterByOrdersId($order_id)
            ->orderByProductsSku()
            ->find()
        ;

        $order_attributes = OrdersAttributesQuery::create()
            ->filterByOrdersId($order_id)
            ->orderByNs()
            ->orderByCKey()
            ->find()
        ;

        // FIXME: pull from orders object
        $form_state = $this->createFormBuilder(array('state' => $order->getState()))
            ->add('state', 'choice',
                array(
                    'choices' => array(
                        -110 => 'Payment error',
                        -100 => 'General error',
                        -50 => 'Building order',
                        -30 => 'Order in pre confirm state',
                        -20 => 'Order in pre payment state',
                        10 => 'Order in post confirm state',
                        20 => 'Order payment confirmed',
                        30 => 'Order pending',
                        40 => 'Order beeing processed',
                        50 => 'Order shipped/done',
                    ),
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
                $order->save();

                $this->get('session')->setFlash('notice', 'admin.orders.state_log.inserted');
            }
        }

        return $this->render('AdminBundle:Orders:view.html.twig', array(
            'order'  => $order,
            'order_lines' => $order_lines,
            'order_attributes' => $order_attributes,
            'form_state' => $form_state->createView()
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

        // $orders = OrdersQuery::create()->find();

        $orders = array();
        return $this->render('AdminBundle:Orders:failed_orders_list.html.twig', array(
            'orders'  => $orders,
        ));
        //
    }


    public function resyncAction($order_id)
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

        $status = $this->get('ax_manager')->sendOrder($order);
        $message = $status ?
            'Ordren er nu sendt' :
            'Ordren kunne ikke gensendes !'
        ;

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => false,
                'message' => 'Der findes ingen ordre med ID #' . $order_id
            ));
        }

        return $this->response('Der findes ingen ordre med ID #' . $order_id);

    }

    public function deleteAction($order_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        $order = OrdersQuery::create()->findOneById($order_id);
        if ($order) {
            $order->delete();
        }
    }

    /**
     * viewFailedAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function viewDeadAction()
    {
        return $this->render('AdminBundle:Orders:dead_orders_list.html.twig');
    }
}
