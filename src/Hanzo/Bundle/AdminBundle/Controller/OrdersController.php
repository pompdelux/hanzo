<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersAttributesQuery;

class OrdersController extends Controller
{

    public function indexAction($id, $pager)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');

        $orders = OrdersQuery::create();

        if(null != $id)
        	$orders = $orders->filterByCustomersId($id);

        if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';

            $orders->filterByCustomersId($q_clean)
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
                    'finishedat' => $order->getFinishedAt(),
                    'totallines' => $orders_count->getVirtualColumn('TotalLines'),
                    'totalprice' => $orders_count->getVirtualColumn('TotalPrice')
                );
            }else{
                $order_data[] = array(
                    'id' => $order->getId(),
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
                $pages[$page] = $router->generate($route, array('id' => $id, 'pager' => $page), TRUE);

            }

            $paginate = array(
                'next' => ($orders->getNextPage() == $pager ? '' : $router->generate($route, array('id' => $id, 'pager' => $orders->getNextPage()), TRUE)),
                'prew' => ($orders->getPreviousPage() == $pager ? '' : $router->generate($route, array('id' => $id, 'pager' => $orders->getPreviousPage()), TRUE)),

                'pages' => $pages,
                'index' => $pager
            );
        }

        return $this->render('AdminBundle:Orders:list.html.twig', array(
            'orders'  => $order_data,
            'paginate' => $paginate
        ));
    }

    public function viewAction($order_id)
    {
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
}
