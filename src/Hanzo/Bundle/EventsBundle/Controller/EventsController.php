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
	Hanzo\Model\EventsParticipants,
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

        return $this->render('EventsBundle:Events:index.html.twig', array(
        	'page_type' => 'calendar'
        ));
	}

    public function getEventsAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
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
    			'color' => (strtotime($event->getEventDate()) >= date()) ? 'green': 'red'
    		);
    	}

    	// Returns directly to the fullCalendar jQuery plugin
        if ($this->getFormat() == 'json') {
            return $this->json_response($events_array);
        }
    }

    public function viewAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('login'));
        }

    	$event = EventsQuery::create()->findPK($id);

        return $this->render('EventsBundle:Events:view.html.twig', array(
        	'page_type' => 'calendar',
            'event' => $event,
            'id'	=> $id
        ));
    }

    public function createAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('login'));
        }

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
                	'attr' => array('class' => 'datetimepicker'),
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

        	$changed = isset($id) ? true : false; // Keep track of which this is a new event or an old event
        	if($changed)
        		$old_event = $event->copy(); // Keep a copy of the old data before we bind the request

            $form->bindRequest($request);

            if ($form->isValid()) {

            	$customers_id = $event->getCustomersId(); // from the form
            	$host = null; // Customers Object
            	$changed_host = false; // Bool wheter the host has changed
            	$new_host = false; // Bool wheter a new Customers have been created

            	// Hvis der er ændret i email = ny host
        		if($changed && ($old_event->getEmail() != $event->getEmail()))
        			$changed_host = true; // Keep track if the host is new/changed
        		$host = CustomersQuery::create()
        			->findOneByEmail($event->getEmail())
        		;

            	// Der er ikke tilknyttet nogle Customers som vært, opret en ny
        		if(!($host instanceof Customers)){
        			$new_host = true;
        			$host = new Customers();
	                $host->setPasswordClear($event->getPhone());
	                $host->setPassword(sha1($event->getPhone()));
	                $host->setEmail($event->getEmail());

	                $host->save();
        		}
        		$event->setCustomersId($host->getId());

            	$consultant = ConsultantsQuery::create()->findPK($this->get('security.context')->getToken()->getUser()->getPrimaryKey());

            	$event->setConsultantsId($consultant->getId());

            	// Its a new event. generate a key to send out to host
            	if(!$changed){
            		$event->setKey(sha1(time()));
            	}

                $event->save(); // Needs to save before we can retrieve the ID for the code :-?

            	// Generate the Code of the event YYYY MM DD INIT TYPE ID DOMAIN
            	$code = 		date('Ymd', strtotime($event->getEventDate()));
            	$code = $code . $consultant->getInitials();
            	$code = $code . $event->getType();
            	$code = $code . $event->getId();
            	$code = $code . $hanzo->get('core.domain_key');
            	$event->setCode(strtoupper($code));

                $event->save();

				$mailer = $this->get('mail_manager');
                if($changed){
                	if($changed_host){ // If the event has changed and its a new host, send specific mails to all
                		if($event->getNotifyHostess()){
                			// Send an email to the old Host
                			$mailer->setMessage('events.hostess.eventmovedfrom', array(
				                'event_date'		=> date('d/m', strtotime($event->getEventDate())),
				                'event_time'		=> date('H:i', strtotime($event->getEventDate())),
				                'name'    	 		=> $old_event->getHost(),
				                'from_address'		=> $old_event->getAddressLine1(). ' ' .$old_event->getAddressLine2(),
				                'from_zip'			=> $old_event->getPostalCode(),
				                'from_city'			=> $old_event->getCity(),
				                'to_name'    	 	=> $event->getHost(),
				                'to_address'		=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
				                'to_zip'			=> $event->getPostalCode(),
				                'to_city'			=> $event->getCity(),
				                'to_phone'			=> $event->getPhone(),
								'link'				=> $this->generateUrl('events_invite', array('key' => $event->getKey()), true),
				                'consultant_name'	=> $consultant->getCustomers()->getFirstName(). ' ' .$consultant->getCustomers()->getLastName(),
				                'consultant_email'	=> $consultant->getCustomers()->getEmail()
				            ));
				            $mailer->setTo(array($old_event->getEmail() => $old_event->getHost()));
				            $mailer->send();

				            // Send an email to the new Host
                			$mailer->setMessage('events.hostess.eventmovedto', array(
				                'event_date'		=> date('d/m', strtotime($event->getEventDate())),
				                'event_time'		=> date('H:i', strtotime($event->getEventDate())),
				                'from_name' 		=> $old_event->getHost(),
				                'from_address'		=> $old_event->getAddressLine1(). ' ' .$old_event->getAddressLine2(),
				                'from_zip'			=> $old_event->getPostalCode(),
				                'from_city'			=> $old_event->getCity(),
				                'from_phone'		=> $old_event->getPhone(),
				                'name'	    	 	=> $event->getHost(),
				                'to_address'		=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
				                'to_zip'			=> $event->getPostalCode(),
				                'to_city'			=> $event->getCity(),
				                'email'				=> $host->getEmail(),
				                'password'			=> $host->getPasswordClear(),
				                'phone'				=> $event->getPhone(),
								'link'				=> $this->generateUrl('events_invite', array('key' => $event->getKey()), true),
				                'consultant_name'	=> $consultant->getCustomers()->getFirstName(). ' ' .$consultant->getCustomers()->getLastName(),
				                'consultant_email'	=> $consultant->getCustomers()->getEmail()
				            ));
				            $mailer->setTo(array($old_event->getEmail() => $old_event->getHost()));
				            $mailer->send();
                		}

						// Find all participants.
		            	$participants = EventsParticipantsQuery::create()
		            		->filterByEventsId($event->getId())
		            		->filterByHasAccepted(true)
		            		->find()
		            	;

                		// Send an email to all participants
                		foreach ($participants as $participant) {

	            			$mailer->setMessage('events.participant.eventchanged', array(
				                'event_date'		=> date('d/m', strtotime($event->getEventDate())),
				                'event_time'		=> date('H:i', strtotime($event->getEventDate())),
				                'to_name'	   		=> $participant->getFirstName(). ' ' .$participant->getLastName(),
				                'hostess'	   		=> $event->getHost(),
				                'address'			=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
				                'zip'				=> $event->getPostalCode(),
				                'city'				=> $event->getCity(),
				                'phone'				=> $event->getPhone(),
				                'email'				=> $event->getEmail(),
				                'link'				=> $this->generateUrl('events_rsvp', array('key' => $participant->getKey()), true),
				                'consultant_name'	=> $consultant->getCustomers()->getFirstName(). ' ' .$consultant->getCustomers()->getLastName(),
				                'consultant_email'	=> $consultant->getCustomers()->getEmail()
				            ));
				            $mailer->setTo(array($participant->getEmail() => $participant->getFirstName(). ' ' .$participant->getLastName()));
				            $mailer->send();
                		}
                	}
                }else{
					if($event->getNotifyHostess()){

			            // Send an email to the new Host
            			$mailer->setMessage('events.hostess.create', array(
			                'event_date'		=> date('d/m', strtotime($event->getEventDate())),
			                'event_time'		=> date('H:i', strtotime($event->getEventDate())),
			                'to_name'    	 	=> $event->getHost(),
			                'address'			=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
			                'zip'				=> $event->getPostalCode(),
			                'city'				=> $event->getCity(),
			                'email'				=> $host->getEmail(),
			                'password'			=> $host->getPasswordClear(),
			                'phone'				=> $event->getPhone(),
							'link'				=> $this->generateUrl('events_invite', array('key' => $event->getKey()), true),
			                'consultant_name'	=> $consultant->getCustomers()->getFirstName(). ' ' .$consultant->getCustomers()->getLastName(),
			                'consultant_email'	=> $consultant->getCustomers()->getEmail()
			            ));
			            $mailer->setTo(array($event->getEmail() => $event->getHost()));
			            $mailer->send();
            		}
                }

                $this->get('session')->setFlash('notice', 'events.created');

                // Its a new event. Redirect to correct url.
                if(!$changed)
                	return $this->redirect($this->generateUrl('events_create',
                		array('id' => $event->getId())
                	));
            }
        }

        return $this->render('EventsBundle:Events:create.html.twig', array(
        	'page_type' => 'calendar',
            'form'      => $form->createView(),
            'id' 		=> $id
        ));
    }

    public function getCustomerAction($email)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
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
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
    	$event = EventsQuery::create()->findPK($id);
    	if($event instanceof Events){
    		$consultant = ConsultantsQuery::create()->joinWithCustomers()->findPK($event->getConsultantsId());
    		// Send some emails for the host and participants
        	$participants = EventsParticipantsQuery::create()
        		->filterByEventsId($event->getId())
        		->filterByHasAccepted(true)
        		->find()
        	;


        	// Now send out some emails!
			$mailer = $this->get('mail_manager');

            $mailer->setMessage('events.hostess.delete', array(
                'event_date'		=> date('d/m', strtotime($event->getEventDate())),
                'event_time'		=> date('H:i', strtotime($event->getEventDate())),
                'to_name'     		=> $event->getHost(),
                'address'			=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                'zip'				=> $event->getPostalCode(),
                'city'				=> $event->getCity(),
                'consultant_name'	=> $consultant->getCustomers()->getFirstName(). ' ' .$consultant->getCustomers()->getLastName(),
                'consultant_email'	=> $consultant->getCustomers()->getEmail()
            ));
            $mailer->setTo(array($event->getEmail() => $event->getHost()));
            $mailer->send();
        	foreach ($participants as $participant) {
	            $mailer->setMessage('events.participants.delete', array(
	                'event_date'	=> date('d/m', strtotime($event->getEventDate())),
	                'event_time'	=> date('H:i', strtotime($event->getEventDate())),
	                'to_name'     	=> $participant->getFirstName(),
	                'address'		=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
	                'zip'			=> $event->getPostalCode(),
	                'city'			=> $event->getCity(),
	                'hostess'		=> $event->getHost(),
	                'hostess_email'	=> $event->getEmail()
	            ));
	            $mailer->setTo($participant->getEmail(), $participant->getFirstName(). ' ' .$participant->getLastName());
	            $mailer->send();
        	}

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

    public function inviteAction($key)
    {
    	$event = EventsQuery::create()
    		->filterByEventDate(array('min' => date('Y-m-d H:i:s', strtotime('+1 day'))))
    		->findOneByKey($key)
    	;

    	$events_participants = null;
    	$form = null;
    	if($event instanceof Events){
    		$consultant = ConsultantsQuery::create()->joinWithCustomers()->findPK($event->getConsultantsId());
    		$events_participant = new EventsParticipants();

	    	$form = $this->createFormBuilder($events_participant)
	    		->add('first_name', 'text',
	                array(
	                    'label' => 'events.participants.first_name.label',
	                    'translation_domain' => 'events'
	                )
	            )->add('last_name', 'text',
	                array(
	                    'label' => 'events.participants.last_name.label',
	                    'translation_domain' => 'events'
	                )
	            )->add('email', 'email',
	                array(
	                    'label' => 'events.participants.email.label',
	                    'translation_domain' => 'events'
	                )
	            )->add('phone', 'text',
	                array(
	                    'label' => 'events.participants.phone.label',
	                    'translation_domain' => 'events',
	                    'required' => false
	                )
	            )->add('tell_a_friend', 'checkbox',
	                array(
	                    'label' => 'events.participants.tell_a_friend.label',
	                    'translation_domain' => 'events',
	                    'required' => false
	                )
	            )->getForm()
	        ;

	        $request = $this->getRequest();
	        if ('POST' === $request->getMethod()) {
	            $form->bindRequest($request);

	            if ($form->isValid()) {
	            	$events_participant->setKey(sha1(time()));
	            	$events_participant->setEventsId($event->getId());
	            	$events_participant->save();

	            	// Now send out some emails!
					$mailer = $this->get('mail_manager');

		            $mailer->setMessage('events.participant.invited', array(
		                'event_date'		=> date('d/m', strtotime($event->getEventDate())),
		                'event_time'		=> date('H:i', strtotime($event->getEventDate())),
		                'to_name'     		=> $events_participant->getFirstName(). ' ' .$events_participant->getLastName(),
		                'hostess'			=> $event->getHost(),
		                'address'			=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
		                'zip'				=> $event->getPostalCode(),
		                'city'				=> $event->getCity(),
		                'email'				=> $event->getEmail(),
		                'phone'				=> $event->getPhone(),
						'link'				=> $this->generateUrl('events_rsvp', array('key' => $events_participant->getKey()), true),
		                'consultant_name'	=> $consultant->getCustomers()->getFirstName(). ' ' .$consultant->getCustomers()->getLastName(),
		                'consultant_email'	=> $consultant->getCustomers()->getEmail()
		            ));

		            $mailer->setTo(
		            	$events_participant->getEmail(),
		            	$events_participant->getFirstName(). ' ' .$events_participant->getLastName()
		            );
		            $mailer->send();

	                $this->get('session')->setFlash('notice', 'events.participant.invited');
	            }
	        }

	        $form = $form->createView();

	        $events_participants = EventsParticipantsQuery::create()->findByEventsId($event->getId());
    	}

        return $this->render('EventsBundle:Events:invite.html.twig', array(
        	'page_type' 	=> 'event',
        	'key'			=> $key,
        	'event'			=> $event,
            'form'      	=> $form,
            'participants'	=> $events_participants
        ));
    }

    public function rsvpAction($key)
    {
    	$events_participant = EventsParticipantsQuery::create()->findOneByKey($key);
    	$event = null;
    	if($events_participant instanceof EventsParticipants){
			$event = EventsQuery::create()
				->filterByEventDate(array('min' => date('Y-m-d H:i:s', strtotime('+1 day'))))
				->findOneById($events_participant->getEventsId())
			;
    	}
    	$form_rsvp = null;
    	$form_tell_a_friend = null;

    	if($events_participant instanceof EventsParticipants && $event instanceof Events){

    		if(true === $events_participant->getTellAFriend()){
	    		$form_tell_a_friend = $this->createFormBuilder(new EventsParticipants())
		    		->add('first_name', 'text',
		                array(
		                    'label' => 'events.participants.first_name.label',
		                    'translation_domain' => 'events'
		                )
		            )->add('last_name', 'text',
		                array(
		                    'label' => 'events.participants.last_name.label',
		                    'translation_domain' => 'events'
		                )
		            )->add('email', 'email',
		                array(
		                    'label' => 'events.participants.email.label',
		                    'translation_domain' => 'events'
		                )
	            	)->add('phone', 'text',
		                array(
		                    'label' => 'events.participants.phone.label',
		                    'translation_domain' => 'events',
		                    'required' => false
		                )
		            )->getForm()
		        ;
	        	$form_tell_a_friend = $form_tell_a_friend->createView();
    		}

	    	$form_rsvp = $this->createFormBuilder($events_participant)
	    		->add('first_name', 'text',
	                array(
	                    'label' => 'events.participants.first_name.label',
	                    'translation_domain' => 'events'
	                )
	            )->add('last_name', 'text',
	                array(
	                    'label' => 'events.participants.last_name.label',
	                    'translation_domain' => 'events'
	                )
	            )->add('phone', 'text',
	                array(
	                    'label' => 'events.participants.phone.label',
	                    'translation_domain' => 'events',
	                    'required' => false
	                )
	            )->add('notify_by_sms', 'checkbox',
	                array(
	                    'label' => 'events.participants.notify_by_sms.label',
	                    'translation_domain' => 'events',
	                    'required' => false
	                )
	            )->add('has_accepted', 'checkbox',
	                array(
	                    'label' => 'events.participants.has_accepted.label',
	                    'translation_domain' => 'events',
	                    'required' => false
	                )
	            )->getForm()
	        ;

	        $request = $this->getRequest();
	        if ('POST' === $request->getMethod()) {
	            $form_rsvp->bindRequest($request);

	            if ($form_rsvp->isValid()) {
	            	$events_participant->setRespondedAt(date('Y-m-d H:i:s'));
	            	$events_participant->save();

	                $this->get('session')->setFlash('notice', 'events.participant.rsvp.success');
	            }
	        }
	        $form_rsvp = $form_rsvp->createView();
    	}

        return $this->render('EventsBundle:Events:rsvp.html.twig', array(
        	'page_type' 			=> 'event',
        	'key'					=> $key,
        	'event'					=> $event,
            'form_rsvp'   			=> $form_rsvp,
            'form_tell_a_friend'  	=> $form_tell_a_friend
        ));
    }

    public function tellAFriendAction($key)
    {

    	$friend = EventsParticipantsQuery::create()
    		->filterByTellAFriend(true)
    		->findOneByKey($key)
    	;

    	if($friend instanceof EventsParticipants){
	    	$event = EventsQuery::create()
	    		->findOneById($friend->getEventsId())
	    	;
    		$consultant = ConsultantsQuery::create()->joinWithCustomers()->findPK($event->getConsultantsId());
    		$events_participant = new EventsParticipants();

			$form = $this->createFormBuilder($events_participant)
	    		->add('first_name', 'text',
	                array(
	                    'label' => 'events.participants.first_name.label',
	                    'translation_domain' => 'events'
	                )
	            )->add('last_name', 'text',
	                array(
	                    'label' => 'events.participants.last_name.label',
	                    'translation_domain' => 'events'
	                )
	            )->add('email', 'email',
	                array(
	                    'label' => 'events.participants.email.label',
	                    'translation_domain' => 'events'
	                )
            	)->add('phone', 'text',
	                array(
	                    'label' => 'events.participants.phone.label',
	                    'translation_domain' => 'events',
	                    'required' => false
	                )
	            )->getForm()
	        ;
	        $request = $this->getRequest();
	        if ('POST' === $request->getMethod()) {
	            $form->bindRequest($request);

	            if ($form->isValid()) {
	            	$events_participant->setKey(sha1(time()));
	            	$events_participant->setEventsId($friend->getEventsId());
	            	$events_participant->setInvitedBy($friend->getId());
	            	$events_participant->save();

	            	// Now send out some emails!
					$mailer = $this->get('mail_manager');

		            $mailer->setMessage('events.participant.invited', array(
		                'event_date'		=> date('d/m', strtotime($event->getEventDate())),
		                'event_time'		=> date('H:i', strtotime($event->getEventDate())),
		                'to_name'     		=> $events_participant->getFirstName(). ' ' .$events_participant->getLastName(),
		                'hostess'			=> $event->getHost(),
		                'address'			=> $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
		                'zip'				=> $event->getPostalCode(),
		                'city'				=> $event->getCity(),
		                'email'				=> $event->getEmail(),
		                'phone'				=> $event->getPhone(),
						'link'				=> $this->generateUrl('events_rsvp', array('key' => $events_participant->getKey()), true),
		                'consultant_name'	=> $consultant->getCustomers()->getFirstName(). ' ' .$consultant->getCustomers()->getLastName(),
		                'consultant_email'	=> $consultant->getCustomers()->getEmail()
		            ));

		            $mailer->setTo(
		            	$events_participant->getEmail(),
		            	$events_participant->getFirstName(). ' ' .$events_participant->getLastName()
		            );
		            $mailer->send();

		            // Make sure that the friend only invites one
		            $friend->setTellAFriend(false);
		            $friend->save();

	                $this->get('session')->setFlash('notice', 'events.participant.invited');
	                return $this->redirect($this->generateUrl('events_rsvp', array('key' => $key)));
	            }
	        }
	    }
	    $this->get('session')->setFlash('notice', 'events.participant.invite.failed');
	    return $this->redirect($this->generateUrl('events_rsvp', array('key' => $key)));
    }
}
