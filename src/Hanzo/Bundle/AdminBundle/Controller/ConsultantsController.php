<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\ConsultantsQuery;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\EventsQuery;

use Propel\Runtime\Parser\PropelCSVParser;

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
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';

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
                    ->_or()
                    ->filterByCustomersId($q_clean)
                    ->orderByFirstName()
                    ->orderByLastName()
                ->endUse()
                ->joinWithCustomers()
                ->paginate($pager, 50)
            ;
            
        } else {

            $consultants = ConsultantsQuery::create()
                ->useCustomersQuery()
                    ->orderByFirstName()
                    ->orderByLastName()
                ->endUse()
                ->joinWithCustomers()
                ->paginate($pager, 50)
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
        $consultant = ConsultantsQuery::create()
            ->joinWithCustomers()
            ->findOneById($id)
        ;
        $customer = $consultant->getCustomers();
        $consultant_data = array(
            'first_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'discount' => $customer->getDiscount(),
            'password_clear' => $customer->getPasswordClear(),
            'is_active' => $customer->getIsActive(),
            'initials' => $consultant->getInitials(),
            'info' => $consultant->getInfo(),
            'event_notes' => $consultant->getEventNotes(),
            'max_notified' => $consultant->getMaxNotified()
        );

        $form = $this->createFormBuilder($consultant_data)
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
            ->add('email', 'email',
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
            ->add('discount', 'number',
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
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )
            ->add('initials', 'text',
                array(
                    'label' => 'admin.consultant.initials.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )
            ->add('info', 'textarea',
                array(
                    'label' => 'admin.consultant.info.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )
            ->add('event_notes', 'textarea',
                array(
                    'label' => 'admin.consultant.event_notes.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )
            ->add('max_notified', 'checkbox',
                array(
                    'label' => 'admin.consultant.max_notified.label',
                    'translation_domain' => 'admin',
                    'required' => false
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
                $data = $form->getData();

                CustomersQuery::create()
                    ->findOneById($id)
                    ->setFirstName($data['first_name'])
                    ->setLastName($data['last_name'])
                    ->setEmail($data['email'])
                    ->setPhone($data['phone'])
                    ->setDiscount($data['discount'])
                    ->setPasswordClear($data['password_clear'])
                    ->setIsActive($data['is_active'])
                    ->save()
                ;
                ConsultantsQuery::create()
                    ->findOneById($id)
                    ->setInitials($data['initials'])
                    ->setInfo($data['info'])
                    ->setEventNotes($data['event_notes'])
                    ->setMaxNotified($data['max_notified'])
                    ->save()
                ;

                $this->get('session')->setFlash('notice', 'consultant.updated');
            }
        }

        return $this->render('AdminBundle:Consultants:view.html.twig', array(
            'form'      => $form->createView(),
            'consultant'  => $consultant
        ));
    }

    public function exportAction($start, $end)
    {
        $parser = new PropelCSVParser();
        $parser->delimiter = ';';

        $date_filter = array();
        if($start && $end){
            $date_filter['min'] = strtotime($start);
            $date_filter['max'] = strtotime($end);
        }else{
            if($start){
                $date_filter['min'] = strtotime('-1 month', strtotime($end));
                $date_filter['max'] = strtotime($end);
            }elseif ($end) {
                $date_filter['min'] = strtotime($end);
                $date_filter['max'] = strtotime('+1 month', strtotime($start));
            }else{
                $date_filter['min'] = strtotime('-1 month', now());
                $date_filter['max'] = strtotime(now());
            }
        }
        $data = array();
        $data[0]['consultant'] = 'consultant';

        $consultants = ConsultantsQuery::create()->joinCustomers()->find(); // Mangler i data[consultants id]

        $events = EventsQuery::create()
            ->filterByEventDate($date_filter)
            ->orderByHost()
            ->find()
        ;

        for ($date=strtotime($start); $date <= strtotime($end); $date = strtotime('+1 day', $date)) { 
            $data[0][$date] = date('Y-m-d', $date); // Header row with visible dates
        }
        foreach ($consultants as $consultant) {
            $customer_data = $consultant->getCustomers(); 
            $data[$consultant->getId()][0] = $customer_data->getFirstName(). ' ' . $customer_data->getLastName();

            for ($date=strtotime($start); $date <= strtotime($end); $date = strtotime('+1 day', $date)) { 
                $data[$consultant->getId()][$date] = '-';
            }

        }

        foreach ($events as $event) {
            $data[$event->getConsultantsId()][strtotime($event->getEventDate())] = $event->getType();
        }

        return new Response( 
            $parser->toCSV($data), 
            200, 
            array( 
                 'Content-Type' => 'text/csv', 
                 'Content-Disposition' => sprintf('attachment; filename="export_' . $start . '-' . $end .'.csv"', 'export_' . $start . '-' . $end .'.csv') 
            ) 
        ); 
    }
}
