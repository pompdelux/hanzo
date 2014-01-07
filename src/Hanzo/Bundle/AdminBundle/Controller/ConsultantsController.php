<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\ConsultantsQuery;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\SettingsQuery;
use Hanzo\Model\Settings;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\Events;

use Propel\Runtime\Parser\PropelCSVParser;

class ConsultantsController extends CoreController
{
    public function indexAction(Request $request, $pager)
    {
        $route       = $request->get('_route');
        $router      = $this->get('router');
        $consultants = null;

        // Search parameter
        if ($request->query->has('q')) {
            $q_clean = $request->query->get('q', null);
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
                    ->filterById($q_clean)
                    ->orderByIsActive('DESC')
                    ->orderByFirstName()
                    ->orderByLastName()
                ->endUse()
                ->joinWithCustomers()
                ->paginate($pager, 50, $this->getDbConnection())
            ;

        } else {

            $consultants = ConsultantsQuery::create()
                ->useCustomersQuery()
                    ->orderByIsActive('DESC')
                    ->orderByFirstName()
                    ->orderByLastName()
                ->endUse()
                ->joinWithCustomers()
                ->paginate($pager, 50, $this->getDbConnection())
            ;

        }
        $paginate = null;
        if ($consultants->haveToPaginate()) {

            $pages = array();
            foreach ($consultants->getLinks(20) as $page) {
                if ($request->query->has('q')) {
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $request->query->get('q')), TRUE);
                } else {
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);
                }

            }

             // If search query, add it to the route
            if ($request->query->has('q')) {
                $paginate = array(
                    'next' => ($consultants->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getNextPage(), 'q' => $request->query->get('q')), TRUE)),
                    'prew' => ($consultants->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getPreviousPage(), 'q' => $request->query->get('q')), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            } else {
                $paginate = array(
                    'next' => ($consultants->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getNextPage()), TRUE)),
                    'prew' => ($consultants->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $consultants->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            }
        }

        $consultant_settings = SettingsQuery::create()
            ->filterByNs('consultant')
            ->findOne($this->getDbConnection())
        ;
        $consultant_settings_data = array();
        if($consultant_settings instanceof Settings){
            foreach ($consultant_settings as $consultant_setting) {
                $consultant_settings_data[$consultant_setting->getCKey()] = $consultant_setting->getCValue();
            }
        }

        $form_settings = $this->createFormBuilder($consultant_settings_data)
            ->add('date', 'date',
                array(
                    'input'              => 'string',
                    'widget'             => 'choice',
                    'label'              => 'admin.consultant.date.label',
                    'translation_domain' => 'admin'
                )
            )->add('max_amount', 'text',
                array(
                    'label'              => 'admin.consultant.max_amount.label',
                    'translation_domain' => 'admin'
                )
            )->getForm()
        ;
        $form_export = $this->createFormBuilder(array(
                'start' => date('Y-m-d', time()),
                'end'   => date('Y-m-d', strtotime('-1 month', time() ))
            ))->add('start', 'date', array(
                'input'              => 'string',
                'widget'             => 'single_text',
                'format'             => 'yy-MM-dd',
                'label'              => 'admin.consultant.export.start.label',
                'translation_domain' => 'admin'
            ))->add('end', 'date', array(
                'input'              => 'string',
                'widget'             => 'single_text',
                'format'             => 'yy-MM-dd',
                'label'              => 'admin.consultant.export.start.label',
                'translation_domain' => 'admin'
            ))->getForm()
        ;

        return $this->render('AdminBundle:Consultants:list.html.twig', array(
            'consultants'         => $consultants,
            'paginate'            => $paginate,
            'consultant_settings' => $form_settings->createView(),
            'form_export'         => $form_export->createView(),
            'database'            => $request->getSession()->get('database')
        ));

    }

    public function viewAction(Request $request, $id)
    {
        $security = $this->get('security.context');

        $consultant = ConsultantsQuery::create()
            ->joinWithCustomers()
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        $customer = $consultant->getCustomers();
        $consultant_data = array(
            'first_name'     => $customer->getFirstName(),
            'last_name'      => $customer->getLastName(),
            'email'          => $customer->getEmail(),
            'phone'          => $customer->getPhone(),
            'discount'       => $customer->getDiscount(),
            'password_clear' => $customer->getPasswordClear(),
            'is_active'      => $customer->getIsActive(),
            'initials'       => $consultant->getInitials(),
            'info'           => $consultant->getInfo(),
            'event_notes'    => $consultant->getEventNotes(),
            'max_notified'   => $consultant->getMaxNotified(),
            'hide_info'      => $consultant->getHideInfo()
        );

        $form = $this->createFormBuilder($consultant_data);
        if ($security->isGranted('ROLE_ADMIN')) {
            $form = $form->add('first_name', 'text',
                array(
                    'label' => 'admin.customer.first_name.label',
                    'translation_domain' => 'admin'
                )
            )->add('last_name', 'text',
                array(
                    'label' => 'admin.customer.last_name.label',
                    'translation_domain' => 'admin'
                )
            )->add('email', 'email',
                array(
                    'label' => 'admin.customer.email.label',
                    'translation_domain' => 'admin'
                )
            )->add('phone', 'text',
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
            )->add('password_clear', 'text', // Puha
                array(
                    'label' => 'admin.customer.password_clear.label',
                    'translation_domain' => 'admin'
                )
            )->add('initials', 'text',
                array(
                    'label' => 'admin.consultant.initials.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('max_notified', 'checkbox',
                array(
                    'label' => 'admin.consultant.max_notified.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            );
        } // END ROLE_ADMIN
        $form = $form->add('is_active', 'checkbox',
                array(
                    'label' => 'admin.customer.is_active.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('info', 'textarea',
                array(
                    'label' => 'admin.consultant.event_notes.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('event_notes', 'textarea',
                array(
                    'label' => 'admin.consultant.info.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('hide_info', 'checkbox',
                array(
                    'label' => 'admin.consultant.hide_info.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                CustomersQuery::create()
                    ->filterById($id)
                    ->findOne($this->getDbConnection())
                    ->setFirstName($data['first_name'])
                    ->setLastName($data['last_name'])
                    ->setEmail($data['email'])
                    ->setPhone($data['phone'])
                    ->setDiscount($data['discount'])
                    ->setPasswordClear($data['password_clear'])
                    ->setPassword(sha1($data['password_clear']))
                    ->setIsActive($data['is_active'])
                    ->save($this->getDbConnection())
                ;
                ConsultantsQuery::create()
                    ->filterById($id)
                    ->findOne($this->getDbConnection())
                    ->setInitials($data['initials'])
                    ->setInfo($data['info'])
                    ->setEventNotes($data['event_notes'])
                    ->setMaxNotified($data['max_notified'])
                    ->setHideInfo($data['hide_info'])
                    ->save($this->getDbConnection())
                ;

                $this->get('session')->getFlashBag()->add('notice', 'consultant.updated');
            }
        }

        return $this->render('AdminBundle:Consultants:view.html.twig', array(
            'form'       => $form->createView(),
            'consultant' => $consultant,
            'database'   => $request->getSession()->get('database')
        ));
    }

    public function indexEventsAction(Request $request, $pager)
    {
        $route  = $request->get('_route');
        $router = $this->get('router');

        $events = EventsQuery::create()
            ->orderByEventDate()
            ->paginate($pager, 50, $this->getDbConnection())
        ;

        $paginate = null;
        if ($events->haveToPaginate()) {

            $pages = array();
            foreach ($events->getLinks(20) as $page) {
                if ($request->query->has('q'))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $request->query->get('q')), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }

            $paginate = array(
                'next' => ($events->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $events->getNextPage()), TRUE)),
                'prew' => ($events->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $events->getPreviousPage()), TRUE)),

                'pages' => $pages,
                'index' => $pager
            );
        }
        return $this->render('AdminBundle:Consultants:listEvents.html.twig', array(
            'events'    => $events,
            'paginate'  => $paginate,
            'start'     => date('d-m-Y', strtotime('-1 month', time() )),
            'end'       => date('d-m-Y', time()),
            'database'  => $request->getSession()->get('database')
        ));
    }

    public function deleteEventAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $event = EventsQuery::create()
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        if($event instanceof Events){
            $event->delete($this->getDbConnection());

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => $this->get('translator')->trans('delete.event.success', array(), 'admin'),
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.event.failed', array(), 'admin'),
            ));
        }
    }

    public function exportAction(Request $request)
    {

        $parser = new \PropelCSVParser();
        $parser->delimiter = ';';
        $start = $end = NULL;
        if ($request->query->has('start') && $request->query->has('end')) {
            $start = $request->query->get('start', null);
            $end   = $request->query->get('end', null);
        }
        $date_filter = array();
        if($start && $end){
            $date_filter['min'] = strtotime($start);
            $date_filter['max'] = strtotime(date("d-m-Y", strtotime($end)) . " +1 day");
        }else{
            $date_filter['min'] = strtotime('-1 month', time());
            $start = $date_filter['min'];
            $date_filter['max'] = strtotime('Tomorrow');
            $end = $date_filter['max'];

        }
        $data = array();
        $data[0]['consultant'] = 'consultant';

        $consultants = ConsultantsQuery::create()
            ->joinCustomers()
            ->useCustomersQuery()
                ->filterByIsActive(TRUE)
                ->filterByGroupsId(2) // Consultants
                ->orderByFirstName()
            ->endUse()
            ->find($this->getDbConnection())
        ;

        $events = EventsQuery::create()
            ->filterByEventDate($date_filter)
            ->orderByHost()
            ->find($this->getDbConnection())
        ;

        for ($date=strtotime($start); $date <= strtotime($end); $date = strtotime('+1 day', $date)) {
            $data[0][date('d-m-Y', $date)] = date('d-m-Y', $date); // Header row with visible dates
        }

        foreach ($consultants as $consultant) {
            $customer_data = $consultant->getCustomers($this->getDbConnection());
            $data[$consultant->getId()][0] = utf8_decode($customer_data->getFirstName(). ' ' . $customer_data->getLastName());

            for ($date=strtotime($start); $date <= strtotime($end); $date = strtotime('+1 day', $date)) {
                $data[$consultant->getId()][date('d-m-Y', $date)] = '-';
            }

        }

        foreach ($events as $event) {
            if(!isset($data[$event->getConsultantsId()])){ // If the consultant is not on the list eg. is inactive
                continue;
            }
            if($data[$event->getConsultantsId()][date('d-m-Y', strtotime($event->getEventDate()))] === '-'){        // If no events yet
                $data[$event->getConsultantsId()][date('d-m-Y', strtotime($event->getEventDate()))] = $event->getType();
            }else{
                $data[$event->getConsultantsId()][date('d-m-Y', strtotime($event->getEventDate()))] .= "+".$event->getType();
            }
        }

        return new Response(
            $parser->toCSV($data, true, false),
            200,
            array(
                 'Content-Type' => 'text/csv',
                 'Content-Disposition' => sprintf('attachment; filename="export_' . $start . '_' . $end .'.csv"', 'export_' . $start . '_' . $end .'.csv')
            )
        );
    }

    public function updateSettingAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $request = $this->getRequest();

        $max_amount = SettingsQuery::create()
            ->filterByNs('consultant')
            ->filterByCKey('max_amount')
            ->findOne($this->getDbConnection())
        ;

        $date = SettingsQuery::create()
            ->filterByNs('consultant')
            ->filterByCKey('date')
            ->findOne($this->getDbConnection())
        ;

        if(!$max_amount){
            $max_amount = new Settings();
            $max_amount->setNs('consultant');
            $max_amount->setCKey('max_amount');
            $max_amount->setTitle('Max Amount');
        }

        if(!$date){
            $date = new Settings();
            $date->setNs('consultant');
            $date->setCKey('date');
            $date->setTitle('Date for Max Amount');
        }

        $max_amount->setCValue($request->get('max_amount'))
            ->save($this->getDbConnection());

        $date->setCValue($request->get('date'))
            ->save($this->getDbConnection());

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin'),
            ));
        }
    }

    public function consultantsOpenhouseAction(Request $request)
    {
        $consultants = ConsultantsQuery::create()
            ->joinCustomers()
            ->useCustomersQuery()
                ->filterByEmail('%@bellcom.dk', \Criteria::NOT_LIKE)
                ->filterByEmail(array('hdkon@pompdelux.dk','mail@pompdelux.dk','hd@pompdelux.dk','kk@pompdelux.dk','sj@pompdelux.dk','ak@pompdelux.dk','test@pompdelux.dk'), \Criteria::NOT_IN)
                ->filterByGroupsId(2)
                ->filterByIsActive(TRUE)
                ->orderByFirstName()
                ->joinAddresses()
                ->useAddressesQuery()
                    ->filterByType('payment')
                ->endUse()
            ->endUse()
            ->select(
                array(
                    'Customers.Id',
                    'Customers.FirstName',
                    'Customers.LastName',
                    'Addresses.PostalCode',
                    'Addresses.City',
                    'Customers.Email',
                    'Customers.Phone',
                    'EventNotes',
                    'HideInfo'
                )
            )
            ->find($this->getDbConnection())
        ;

        $consultants_array = array();
        $cdn = Hanzo::getInstance()->get('core.cdn');

        foreach ($consultants as $consultant) {
            $info = str_replace("\n", "<br>", $consultant['EventNotes']);
            $info = str_replace('src="/', 'src="'.$cdn, $info);

            $consultants_array[] = array(
                'id'        => $consultant['Customers.Id'],
                'name'      => $consultant['Customers.FirstName'] .' '. $consultant['Customers.LastName'],
                'zip'       => $consultant['Addresses.PostalCode'],
                'city'      => $consultant['Addresses.City'],
                'email'     => $consultant['Customers.Email'],
                'phone'     => $consultant['Customers.Phone'],
                'info'      => $info,
                'hide_info' => $consultant['HideInfo'],
            );
        }

        return $this->render('AdminBundle:Consultants:openHouseList.html.twig', array(
            'consultants' => $consultants_array,
            'database'    => $request->getSession()->get('database')
        ));
    }

    public function consultantsFrontpageEditAction(Request $request)
    {
        $setting = SettingsQuery::create()
            ->filterByNs('c')
            ->filterByCKey('frontpage')
            ->findOne($this->getDbConnection())
        ;
        if(!$setting instanceof Settings){
            $setting = new Settings();
            $setting->setNs('c')
                ->setCKey('frontpage')
                ->setTitle('Consultant Frontpage Content')
            ;
        }

        $form = $this->createFormBuilder(array('content' => $setting->getCValue()))
            ->add('content', 'textarea',
                array(
                    'label' => 'admin.consultants.frontpage.content.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $form_data = $form->getData();

                $setting->setCValue($form_data['content']);
                $setting->save($this->getDbConnection());

                $cache = $this->get('cache_manager');
                $cache->clearRedisCache();

                $this->get('session')->getFlashBag()->add('notice', 'admin.consultants.fronpage.content.updated');
            }
        }

        return $this->render('AdminBundle:Consultants:consultantsFrontpageEdit.html.twig', array(
            'form'     => $form->createView(),
            'database' => $request->getSession()->get('database')
        ));
    }
}
