<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
Hanzo\Core\Tools;

use Hanzo\Model\CustomersQuery,
Hanzo\Model\OrdersQuery,
Hanzo\Model\OrdersLinesQuery,
Hanzo\Model\OrdersAttributesQuery;

class CustomersController extends Controller
{
    
    public function indexAction($pager)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');
        $customers = null;

        if (isset($_GET['q'])) {
            $q = $this->getRequest()->get('q', null);
            $q = '%'.$q.'%';
            /**
             * @todo Lav søgning så man kan søge på hele navn. Sammenkobling på for og efternavn.
             */
            $customers = CustomersQuery::create()
                ->useGroupsQuery()
                    ->filterByName('customer')
                    ->_or()
                    ->filterByName('consultant')
                ->endUse()
                ->filterByFirstname($q)
                ->_or()
                ->filterByLastname($q)
                ->_or()
                ->filterByEmail($q)
                ->_or()
                ->filterByPhone($q)
                ->orderByFirstName()
                ->orderByLastName()
                ->paginate($pager, 10)
            ;
        } else {

            $customers = CustomersQuery::create()
                ->useGroupsQuery()
                    ->filterByName('customer')
                    ->_or()
                    ->filterByName('consultant')
                ->endUse()
                ->orderByFirstName()
                ->orderByLastName()
                ->paginate($pager, 10)
            ;
        }
        $paginate = null;
        if ($customers->haveToPaginate()) {

            $pages = array();
            foreach ($customers->getLinks(20) as $page) {
                if (isset($_GET['q']))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }
            
            if (isset($_GET['q'])) // If search query, add it to the route
                $paginate = array(
                    'next' => ($customers->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $customers->getNextPage(), 'q' => $_GET['q']), TRUE)),
                    'prew' => ($customers->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $customers->getPreviousPage(), 'q' => $_GET['q']), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            else
                $paginate = array(
                    'next' => ($customers->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $customers->getNextPage()), TRUE)),
                    'prew' => ($customers->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $customers->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
        }

        return $this->render('AdminBundle:Customers:list.html.twig', array(
            'customers'     => $customers,
            'paginate'      => $paginate
        ));

    }

    public function viewAction($id)
    {
        $customer = CustomersQuery::create()
            ->findOneById($id)
        ;

        $form = $this->createFormBuilder($customer)
            ->add('first_name', 'text',
                array(
                    'label' => 'admin.customer.first_name.label',
                    'translation_domain' => 'admin'
                )
            )
            ->add('last_name', 'text',
                array(
                    'label' => 'admin.customer.last_name.label',
                    'translation_domain' => 'admin'
                )
            )
            ->add('email', 'text',
                array(
                    'label' => 'admin.customer.email.label',
                    'translation_domain' => 'admin'
                )
            )
            ->add('phone', 'text',
                array(
                    'label' => 'admin.customer.phone.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )
            ->add('discount', 'text',
                array(
                    'label' => 'admin.customer.discount.label',
                    'translation_domain' => 'admin'
                )
            )
            ->add('password_clear', 'text', // Puha
                array(
                    'label' => 'admin.customer.password_clear.label',
                    'read_only' => true,
                    'translation_domain' => 'admin'
                )
            )
            ->add('is_active', 'checkbox',
                array(
                    'label' => 'admin.customer.is_active.label',
                    'translation_domain' => 'admin'
                )
            )
            ->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                /**
                 * @todo Synkronisering til AX
                 */

                $customer->save();

                $this->get('session')->setFlash('notice', 'customer.updated');
            }
        }

        return $this->render('AdminBundle:Customers:view.html.twig', array(
            'form'      => $form->createView(),
            'customer'  => $customer
        ));
    }

    public function ordersListAction($id, $pager)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');

        $orders = OrdersQuery::create()
            ->filterByCustomersId($id)
            ->orderByCreatedAt()
            ->orderById()
            ->paginate($pager, 10)
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

            $order_data[] = array(
                'id' => $order->getId(),
                'finishedat' => $order->getFinishedAt(),
                'totallines' => $orders_count->getTotalLines(),
                'totalprice' => $orders_count->getTotalPrice()
            );

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

        return $this->render('AdminBundle:Customers:ordersList.html.twig', array(
            'orders'  => $order_data,
            'paginate' => $paginate
        ));
    }

    public function orderViewAction($order_id)
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

        return $this->render('AdminBundle:Customers:orderView.html.twig', array(
            'order'  => $order,
            'order_lines' => $order_lines,
            'order_attributes' => $order_attributes
        ));
    }
}
