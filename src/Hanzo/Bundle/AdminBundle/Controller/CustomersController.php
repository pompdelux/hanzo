<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
Hanzo\Core\Tools;

use Hanzo\Model\CustomersQuery,
    Hanzo\Model\Addresses,
    Hanzo\Model\AddressesQuery;

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
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';
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
                ->_or()
                ->filterById($q_clean)
                ->orderByFirstName()
                ->orderByLastName()
                ->paginate($pager, 50)
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
                ->paginate($pager, 50)
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

        $addresses = AddressesQuery::create()->findByCustomersId($id);

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
            'customer'  => $customer,
            'addresses' => $addresses
        ));
    }

    public function editAddressAction($id, $type)
    {
        $address = null;
        if($type){
            $address = AddressesQuery::create()
                ->filterByType($type)
                ->findOneByCustomersId($id);
        }else{
            $address = new Addresses();
        }

        $form = $this->createFormBuilder($address)
            ->add('first_name', 'text',
                array(
                    'label' => 'admin.customers.addresses.first_name',
                    'translation_domain' => 'admin'
                )
            )->add('last_name', 'text',
                array(
                    'label' => 'admin.customers.addresses.last_name',
                    'translation_domain' => 'admin'
                )
            )->add('address_line_1', 'text',
                array(
                    'label' => 'admin.customers.addresses.address_line_1',
                    'translation_domain' => 'admin'
                )
            )->add('address_line_2', 'text',
                array(
                    'label' => 'admin.customers.addresses.address_line_2',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('postal_code', 'text',
                array(
                    'label' => 'admin.customers.addresses.postal_code',
                    'translation_domain' => 'admin'
                )
            )->add('city', 'text',
                array(
                    'label' => 'admin.customers.addresses.city',
                    'translation_domain' => 'admin'
                )
            )->add('country', 'text',
                array(
                    'label' => 'admin.customers.addresses.country',
                    'translation_domain' => 'admin'
                )
            )->add('state_province', 'text',
                array(
                    'label' => 'admin.customers.addresses.state_province',
                    'translation_domain' => 'admin'
                )
            )->add('company_name', 'text',
                array(
                    'label' => 'admin.customers.addresses.company_name',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('latitude', 'text',
                array(
                    'label' => 'admin.customers.addresses.latitude',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('longitude', 'text',
                array(
                    'label' => 'admin.customers.addresses.longitude',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $address->save();

                $this->get('session')->setFlash('notice', 'address.updated');
            }
        }

        return $this->render('AdminBundle:Customers:editAddress.html.twig', array(
            'form'      => $form->createView(),
            'address'   => $address
        ));
    }
}
