<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\ConsultantsQuery;

class ConsultantsController extends Controller
{
    
    public function indexAction($pager)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');
        $consultants = null;

        // Search parameter
        if (isset($_GET['q'])) {
            $q = $this->getRequest()->get('q', null);
            $q = '%'.$q.'%';

            /**
             * @todo Lav søgning så man kan søge på hele navn. Sammenkobling på for og efternavn.
             */
            $consultants = ConsultantsQuery::create()
                ->useCustomersQuery()
                    ->filterByFirstname($q)
                    ->_or()
                    ->filterByLastname($q)
                    ->_or()
                    ->filterByEmail($q)
                    ->_or()
                    ->filterByPhone($q)
                    ->orderByFirstName()
                    ->orderByLastName()
                ->endUse()
                ->joinWithCustomers()
                ->paginate($pager, 10)
            ;
            
        } else {

            $consultants = ConsultantsQuery::create()
                ->useCustomersQuery()
                    ->orderByFirstName()
                    ->orderByLastName()
                ->endUse()
                ->joinWithCustomers()
                ->paginate($pager, 10)
            ;

        }
        $paginate = null;
        if ($consultants->haveToPaginate()) {

            $pages = array();
            foreach ($consultants->getLinks(20) as $page) {
                if (isset($_GET['q']))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }

            if (isset($_GET['q'])) // If search query, add it to the route
                $paginate = array(
                    'next' => ($consultants->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getNextPage(), 'q' => $_GET['q']), TRUE)),
                    'prew' => ($consultants->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getPreviousPage(), 'q' => $_GET['q']), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            else
                $paginate = array(
                    'next' => ($consultants->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getNextPage()), TRUE)),
                    'prew' => ($consultants->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
        }

        return $this->render('AdminBundle:Consultants:list.html.twig', array(
            'consultants'     => $consultants,
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
