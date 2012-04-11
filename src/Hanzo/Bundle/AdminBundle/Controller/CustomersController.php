<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\CustomersQuery;

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
        }else{

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
                $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);
            }

            $paginate = array(
                'next' => '',//($customers->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage()), TRUE)),
                'prew' => '',//($customers->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage()), TRUE)),

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
                    'label' => 'admin.customer.email.label',
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
                 * @todo Skal der laves noget MD5 på password? hvad nu hvis man ændre i password_clear?
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
}
