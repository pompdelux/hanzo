<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\EventsQuery,
	Hanzo\Model\Events;

class CalendarController extends CoreController
{
	public function indexAction()
	{
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
    	$event = null;
    	if($id){
    		$event = EventsQuery::create()->findPK($id);
    	}else{
    		$event = new Events();
    	}
    	$form = $this->createFormBuilder($event)
            ->add('event_date', 'date',
                array(
                	'input' => 'string',
                	'widget' => 'single_text',
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
                    'translation_domain' => 'events'
                )
            )->add('postal_code', 'integer',
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
                    'translation_domain' => 'events'
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
                    'translation_domain' => 'events'
                )
            )->add('notify_hostess', 'checkbox',
                array(
                    'label' => 'events.notify_hostess.label',
                    'translation_domain' => 'events'
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
              
                $event->save();

                $this->get('session')->setFlash('notice', 'zip_to_city.updated');
            }
        }

        return $this->render('EventsBundle:Calendar:create.html.twig', array(
        	'page_type' => 'calendar',
            'form'      => $form->createView(),
            'id' 		=> $id
        ));
    }
}
