<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\EventsQuery,
	Hanzo\Model\Events,
	Hanzo\Model\EventsParticipantsQuery,
	Hanzo\Model\ConsultantsQuery,
	Hanzo\Model\CustomersQuery,
	Hanzo\Model\Customers;

class EventsController extends CoreController
{
	public function indexAction()
	{
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('login'));
        }

        return $this->render('EventsBundle:Calendar:index.html.twig', array(
        	'page_type' => 'calendar'
        ));
	}

    public function getEventsAction()
    {
    	$start = $this->getRequest()->get('start', null);
    	$end = $this->getRequest()->get('end', null);

        $date_filter['min'] =  gmdate("Y-m-d H:i:s", $start);
        $date_filter['max'] =  gmdate("Y-m-d H:i:s", $end);
    	$events = EventsQuery::create()
    		->filterByEventDate($date_filter)
    		->filterByConsultantsId($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
    		->find()
    	;

    	$events_array = array();

    	foreach ($events as $event) {
    		$events_array[] = array(
    			'id' => $event->getId(),
    			'title' => $event->getCode(),
    			'allDay' => false,
    			'start' => strtotime($event->getEventDate()),
    			'url' => $this->get('router')->generate('events_view', array('id' => $event->getId())),
    			'className' => $event->getType(),
    			'editable' => false,
    			'color' => ($event->getIsOpen() == true) ? 'green': 'red'
    		);
    	}

    	// Returns directly to the fullCalendar jQuery plugin
        if ($this->getFormat() == 'json') {
            return $this->json_response($events_array);
        }
    }

    public function viewAction($id)
    {
    	$event = EventsQuery::create()->findPK($id);

        return $this->render('EventsBundle:Calendar:view.html.twig', array(
        	'page_type' => 'calendar',
            'event' => $event,
            'id'	=> $id
        ));
    }

    public function createAction($id)
    {
        $hanzo = Hanzo::getInstance();

    	$event = null;
    	if($id){
    		$event = EventsQuery::create()->findPK($id);
    	}else{
    		$event = new Events();
    	}
    	$form = $this->createFormBuilder($event)
    		->add('customers_id', 'hidden')
    		->add('event_date', 'datetime',
                array(
                	'input' => 'string',
                	'widget' => 'single_text',
    				'date_format' => 'yyyy-MM-dd hh:mm',
                	'attr' => array('class' => 'datepicker'),
                    'label' => 'events.event_date.label',
                    'translation_domain' => 'events'
                )
            )->add('host', 'text',
                array(
                    'label' => 'events.host.label',
                    'translation_domain' => 'events'
                )
            )->add('address_line_1', 'text',
                array(
                    'label' => 'events.address_line_1.label',
                    'translation_domain' => 'events'
                )
            )->add('address_line_2', 'text',
                array(
                    'label' => 'events.address_line_2.label',
                    'translation_domain' => 'events',
                    'required' => false
                )
            )->add('postal_code', 'text',
                array(
                    'label' => 'events.postal_code.label',
                    'translation_domain' => 'events'
                )
            )->add('city', 'text',
                array(
                    'label' => 'events.city.label',
                    'translation_domain' => 'events'
                )
            )->add('phone', 'text',
                array(
                    'label' => 'events.phone.label',
                    'translation_domain' => 'events'
                )
            )->add('email', 'text',
                array(
                    'label' => 'events.email.label',
                    'translation_domain' => 'events'
                )
            )->add('description', 'textarea',
                array(
                    'label' => 'events.description.label',
                    'translation_domain' => 'events',
                    'required' => false
                )
            )->add('type', 'choice',
                array(
                	'choices' => array(
                		'AR' => 'events.type.choice.ar',
                		'HUS' => 'events.type.choice.hus',
                	),
                    'label' => 'events.type.label',
                    'translation_domain' => 'events'
                )
            )->add('is_open', 'checkbox',
                array(
                    'label' => 'events.is_open.label',
                    'translation_domain' => 'events',
                    'required' => false
                )
            )->add('notify_hostess', 'checkbox',
                array(
                    'label' => 'events.notify_hostess.label',
                    'translation_domain' => 'events',
                    'required' => false
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {

            	$customers_id = $event->getCustomersId();
            	$customer = null;
            	// Der er ikke tilknyttet nogle Customers som vært. Gør et forsøg at finde en, eller opret en ny
            	if(empty($customers_id)){

            		$customer = CustomersQuery::create()
            			->findOneByEmail($event->getEmail())
            		;

            		if(!($customer instanceof Customers)){
            			$customer = new Customers();
		                $customer->setPasswordClear($event->getPhone());
		                $customer->setPassword(sha1($event->getPhone()));
		                $customer->setEmail($event->getEmail());

		                $customer->save();
            		}
            		$event->setCustomersId($customer->getId());
            	}

            	$consultant = ConsultantsQuery::create()->findPK($this->get('security.context')->getToken()->getUser()->getPrimaryKey());

            	$event->setConsultantsId($consultant->getId());

            	// Its a new event. generate a key to send out to host
            	if(!$id){
            		$event->setKey(sha1(time()));
            	}

                $event->save();
            	// Generate the Code of the event YYYY MM DD INIT TYPE ID DOMAIN
            	$code = date('Ymd', strtotime($event->getEventDate()));
            	$code = $code . $consultant->getInitials();
            	$code = $code . $event->getType();
            	$code = $code . $event->getId();
            	$code = $code . $hanzo->get('core.domain_key');
            	$event->setCode(strtoupper($code));
                
                $event->save();

                // Send some emails for the host and participants
                if($event->getNotifyHostess()){
	                // Find all participants.
	            	$participants = EventsParticipantsQuery::create()
	            		->filterByEventsId($event->getId())
	            		->filterByHasAccepted(true)
	            		->find()
	            	;
	            	$participants_array = array($event->getEmail() => $event->getHost());

	            	foreach ($participants as $participant) {
	            		$participants_array[$participant->getEmail()] = $participant->getFirstName(). ' ' .$participant->getLastName();
	            	}

	            	// Now send out some emails!
					$mailer = $this->get('mail_manager');

					// The event is new, set message
					if(!$id){
						$mailer->setMessage('events.created', array(
		                    'name'     => $event->getHost(),
		                ));
					}else{
		                $mailer->setMessage('events.updated', array(
		                    'name'     => $event->getHost(),
		                ));
					}
	                //$mailer->setTo(array($event->getEmail() => $event->getHost()));
	                $mailer->setBcc($participants_array);
	                $mailer->send();
           		}

                $this->get('session')->setFlash('notice', 'events.created');

                // Its a new event. Redirect to correct url.
                if(!$id)
                	return $this->redirect($this->generateUrl('events_create', 
                		array('id' => $event->getId())
                	));
            }
        }

        return $this->render('EventsBundle:Calendar:create.html.twig', array(
        	'page_type' => 'calendar',
            'form'      => $form->createView(),
            'id' 		=> $id
        ));
    }

    public function getCustomerAction($email)
    {
    	$customer = CustomersQuery::create()->findOneByEmail($email);

    	if($customer instanceof Customers){
    		if ($this->getFormat() == 'json') {
	            return $this->json_response(array(
	            	'status' => true,
	            	'message' => $this->get('translator')->trans('events.customer.found', array(), 'events'),
	            	'data' => array(
	            		'id' => $customer->getId(),
	            		'name' => $customer->getFirstName().' '.$customer->getLastName(),
	            		'phone' => $customer->getPhone(),
	            		'email' => $customer->getEmail()
	            	)
	            ));
	        }
    	}

    	if ($this->getFormat() == 'json') {
            return $this->json_response(array(
            	'status' => false,
            	'message' => $this->get('translator')->trans('events.customer.notfound', array(), 'events')
            ));
        }
    }

    public function deleteAction($id)
    {
    	$event = EventsQuery::create()->findPK($id);

    	if($event instanceof Events){
    		// Send some emails for the host and participants
        	$participants = EventsParticipantsQuery::create()
        		->filterByEventsId($event->getId())
        		->filterByHasAccepted(true)
        		->find()
        	;
        	$participants_array = array($event->getEmail() => $event->getHost());

        	foreach ($participants as $participant) {
        		$participants_array[$participant->getEmail()] = $participant->getFirstName(). ' ' .$participant->getLastName();
        	}

        	// Now send out some emails!
			$mailer = $this->get('mail_manager');

            $mailer->setMessage('events.deleted', array(
                'name'     => $event->getHost(),
            ));
			
            //$mailer->setTo(array($event->getEmail() => $event->getHost()));
            $mailer->setBcc($participants_array);
            $mailer->send();
            
    		$event->delete();
    	}

    	if ($this->getFormat() == 'json') {
            return $this->json_response(array(
            	'status' => true,
            	'message' => $this->get('translator')->trans('events.delete.success', array(), 'events')
            ));
        }
        
        $this->get('session')->setFlash('notice', 'events.delete.success');

        return $this->redirect($this->generateUrl('events_index'));
    }
}
