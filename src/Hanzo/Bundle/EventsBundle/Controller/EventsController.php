<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Hanzo\Bundle\EventsBundle\Form\Type\EventsType;
use Hanzo\Bundle\EventsBundle\Helpers\EventHostess;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\Events;
use Hanzo\Model\EventsParticipantsQuery;
use Hanzo\Model\CustomersPeer;

class EventsController extends CoreController
{
    public function indexAction()
    {
        $hanzo = Hanzo::getInstance();
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $hanzo->get('core.locale')]));
        }

        $date_filter['max'] = date('Y-m-d H:i:s');
        $archived_events = EventsQuery::create()
            ->filterByEventDate($date_filter)
            ->filterByConsultantsId($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
            ->orderByEventDate('DESC')
            ->find()
        ;

        return $this->render('EventsBundle:Events:index.html.twig', array(
            'page_type' => 'calendar',
            'archived_events' => $archived_events
        ));
    }

    public function getEventsAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') &&
            false === $this->get('security.context')->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException();
        }

        $start = $request->query->get('start', null);
        $end   = $request->query->get('end', null);

        $date_filter['min'] =  gmdate("Y-m-d H:i:s", $start);
        $date_filter['max'] =  gmdate("Y-m-d H:i:s", $end);
        $events = EventsQuery::create()
            ->filterByEventDate($date_filter)
            ->filterByConsultantsId($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
            ->find()
        ;

        $events_array = array();

        foreach ($events as $event) {

            $color = 'red';
            if (1 == $event->getIsOpen()) {
                $color = 'green';
                if ('HUS' == strtoupper($event->getType())) {
                    $color = 'blue';
                }
            }

            $events_array[] = array(
                'id' => $event->getId(),
                'title' => $event->getCode(),
                'allDay' => false,
                'start' => $event->getEventDate('c'),
                'url' => $this->get('router')->generate('events_view', array('id' => $event->getId())),
                'className' => $event->getType(),
                'editable' => false,
                'color' => $color,
            );
        }

        // Returns directly to the fullCalendar jQuery plugin
        if ($this->getFormat() == 'json') {
            return $this->json_response($events_array);
        }
    }

    public function viewAction($id)
    {
        $hanzo = Hanzo::getInstance();
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $hanzo->get('core.locale')]));
        }

        $event = EventsQuery::create()->findPK($id);

        $events_participants = EventsParticipantsQuery::create()->findByEventsId($event->getId());

        return $this->render('EventsBundle:Events:view.html.twig', array(
            'page_type' => 'calendar',
            'event' => $event,
            'participants'  => $events_participants,
            'id'    => $id
        ));
    }

    public function createAction(Request $request, $id)
    {
        $hanzo = Hanzo::getInstance();
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $hanzo->get('core.locale')]));
        }

        $hanzo = Hanzo::getInstance();

        $event = null;
        if ($id) {
            $event = EventsQuery::create()->findPK($id);

            // no editing old events
            if ($event->getEventDate('U') < time()) {
                $request->getSession()->getFlashBag()->add('notice', 'event.too.old.to.edit');

                return $this->redirect($this->generateUrl('events_index'));
            }
        } else {
            $event = new Events();
        }

        $form = $this->createForm(new EventsType(), $event);
        $form->get('event_date')->setData($event->getEventDate('m/d/Y H:i'));

        if ('POST' === $request->getMethod()) {

            $changed = $event->isNew();
            if ($changed) {
                $oldEvent = $event->copy();
            }

            $form->handleRequest($request);

            if ($form->isValid()) {
                $consultant = CustomersPeer::getCurrent();

                $host         = null;
                $changedHost = false;

                // Hvis der er ændret i email = ny host
                if ($changed && ($oldEvent->getEmail() != $event->getEmail())) {
                    $changedHost = true;
                }

                $hostess = new EventHostess($event);
                $host = $hostess->getHostess();

                $event->setCustomersId($host->getId());
                $event->setConsultantsId($consultant->getId());

                // Its a new event. generate a key to send out to host
                if (!$changed){
                    $event->setKey(sha1(time()));
                }

                // Needs to save before we can retrieve the ID for the code :-?
                $event->setCode(uniqid('tmp.', true));
                $event->setIsOpen(true);
                $event->save();

                // Generate the Code of the event YYYY MM DD INIT TYPE ID DOMAIN
                $code =         $event->getEventDate('Ymd');
                $code = $code . $consultant->getInitials();
                $code = $code . $event->getType();
                $code = $code . $event->getId();
                $code = $code . str_replace('Sales', '', $hanzo->get('core.domain_key'));
                $event->setCode(strtoupper($code));

                $event->save();

                $mailer = $this->container->get('hanzo.event.mailer');
                $mailer->setEventData($event, $hostess, $consultant);

                if ($changed) {
                    if ($changedHost) {
                        $mailer->sendChangeHostessEmail($oldEvent);
                        $mailer->sendChangeHostessParticipantEmail();
                    }
                } else {
                    $mailer->sendHostessEmail();
                }

                $this->get('session')->getFlashBag()->add('notice', 'events.created');
                return $this->redirect($this->generateUrl('events_index'));
            }
        }

        return $this->render('EventsBundle:Events:create.html.twig', array(
            'page_type' => 'calendar',
            'form'      => $form->createView(),
            'id'        => $id
        ));
    }

    public function getCustomerAction($email)
    {
        if ((false === $this->get('security.context')->isGranted('ROLE_CONSULTANT')) &&
            (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
        ) {
            throw new AccessDeniedException();
        }

        $customer = CustomersQuery::create()->findOneByEmail(str_replace(' ', '+', $email));

        if ($customer instanceof Customers) {
            $c = new \Criteria();
            $c->add(AddressesPeer::TYPE, 'payment');
            $address = $customer->getAddressess()->getFirst();

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => true,
                    'message' => $this->get('translator')->trans('events.customer.found', array(), 'events'),
                    'data' => array(
                        'id' => $customer->getId(),
                        'name' => $customer->getFirstName().' '.$customer->getLastName(),
                        'phone' => $customer->getPhone(),
                        'email' => $customer->getEmail(),
                        'address' => $address->getAddressLine1(),
                        'zip' => $address->getPostalCode(),
                        'city' => $address->getCity()
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


    public function closeAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $event = EventsQuery::create()->findPK($id);
        if ($event instanceof Events) {
            $event->setIsOpen(false);
            $event->save();
        }

        $this->getRequest()->getSession()->getFlashBag()->add('notice', $this->get('translator')->trans('event.closed', array(), 'events'));
        return $this->redirect($this->generateUrl('events_index'));
    }


    public function deleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $event = EventsQuery::create()->findPK($id);
        if ($event instanceof Events){

            // no deleting old events
            if ($event->getEventDate('U') < time()) {
                $this->get('session')->getFlashBag()->add('notice', 'event.too.old.to.delete');
                return $this->redirect($this->generateUrl('events_index'));
            }

            $consultant = CustomersQuery::create()->joinWithConsultants()->findPK($event->getConsultantsId());
            // Send some emails for the host and participants
            $participants = EventsParticipantsQuery::create()
                ->filterByEventsId($event->getId())
                ->find()
            ;

            // Now send out some emails!
            $mailer = $this->get('mail_manager');

            $mailer->setMessage('events.hostess.delete', array(
                'event_date'       => $event->getEventDate('d/m'),
                'event_time'       => $event->getEventDate('H:i'),
                'to_name'          => $event->getHost(),
                'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                'zip'              => $event->getPostalCode(),
                'city'             => $event->getCity(),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ));

            $mailer->setTo(array($event->getEmail() => $event->getHost()));
            $mailer->setFrom(array($consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()));
            $mailer->send();

            foreach ($participants as $participant) {
                if (!$participant->getEmail()) {
                    continue;
                }

                $mailer->setMessage('events.participants.delete', array(
                    'event_date'    => $event->getEventDate('d/m'),
                    'event_time'    => $event->getEventDate('H:i'),
                    'to_name'       => $participant->getFirstName(),
                    'address'       => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                    'zip'           => $event->getPostalCode(),
                    'city'          => $event->getCity(),
                    'hostess'       => $event->getHost(),
                    'hostess_email' => $event->getEmail()
                ));

                $mailer->setTo($participant->getEmail(), $participant->getFirstName(). ' ' .$participant->getLastName());
                $mailer->setFrom(array('events@pompdelux.com' => $event->getHost() . ' (via POMPdeLUX)'));
                $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                $mailer->setReplyTo($event->getEmail(), $event->getHost());
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

        $this->get('session')->getFlashBag()->add('notice', 'events.delete.success');

        return $this->redirect($this->generateUrl('events_index'));
    }

    public function inviteAction($key)
    {
        $customer = CustomersPeer::getCurrent();
        $event = EventsQuery::create()
            ->filterByEventDate(array('min' => date('Y-m-d H:i:s')))
            ->filterByCustomersId($customer->getId())
            ->findOneByKey($key)
        ;

        $events_participants = null;
        $form = null;

        if ($event instanceof Events) {
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
                        'required' => false,
                        'translation_domain' => 'events'
                    )
                )->add('phone', 'text',
                    array(
                        'label' => 'events.participants.phone.label',
                        'translation_domain' => 'events',
                        'required' => false,
                        'attr' => array('class' => 'dk')
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
                $form->handleRequest($request);

                if ($events_participant->getEmail()) {
                    $res = EventsParticipantsQuery::create()
                        ->filterByEventsId($event->getId())
                        ->findByEmail($events_participant->getEmail())
                    ;

                    if ($res->count()) {
                        $error = new FormError($this->get('translator')->trans('event.email.exists', array(), 'events'));
                        $form->addError($error);
                    }
                }

                if ($form->isValid()) {
                    $events_participant->setKey(sha1(time()));
                    $events_participant->setEventsId($event->getId());
                    $events_participant->save();

                    // Now send out some emails!
                    if ($events_participant->getEmail()) {
                        $mailer = $this->get('mail_manager');

                        $mailer->setMessage('events.participant.invited', array(
                            'event_date'       => $event->getEventDate('d/m'),
                            'event_time'       => $event->getEventDate('H:i'),
                            'to_name'          => $events_participant->getFirstName(). ' ' .$events_participant->getLastName(),
                            'hostess'          => $event->getHost(),
                            'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                            'zip'              => $event->getPostalCode(),
                            'city'             => $event->getCity(),
                            'email'            => $event->getEmail(),
                            'phone'            => $event->getPhone(),
                            'link'             => $this->generateUrl('events_rsvp', array('key' => $events_participant->getKey()), true)
                        ));

                        $mailer->setTo(
                            $events_participant->getEmail(),
                            $events_participant->getFirstName(). ' ' .$events_participant->getLastName()
                        );
                        $mailer->setFrom(array('events@pompdelux.com' => $event->getHost(). ' (via POMPdeLUX)'));
                        $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                        $mailer->setReplyTo($event->getEmail(), $event->getHost());
                        $mailer->send();
                    }

                    if ($events_participant->getPhone()) {
                        $this->get('sms_manager')->sendEventInvite($events_participant);
                    }

                    $this->get('session')->getFlashBag()->add('notice', 'events.participant.invited');
                }
            }

            $form = $form->createView();

            $events_participants = EventsParticipantsQuery::create()->findByEventsId($event->getId());
        }

        return $this->render('EventsBundle:Events:invite.html.twig', array(
            'page_type'    => 'event',
            'key'          => $key,
            'event'        => $event,
            'form'         => $form,
            'participants' => $events_participants
        ));
    }

    public function rsvpAction($key)
    {
        $events_participant = EventsParticipantsQuery::create()->findOneByKey($key);
        $event = null;
        if($events_participant instanceof EventsParticipants){
            $event = EventsQuery::create()
                ->filterByEventDate(array('min' => date('Y-m-d H:i:s')))
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
                            'translation_domain' => 'events',
                            'required' => false
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

            $accept = 1;
            if ($events_participant->getRespondedAt()) {
                $accept = $events_participant->getHasAccepted();
            }

            $form_rsvp = $this->createFormBuilder($events_participant)
                ->add('first_name', 'text', array(
                    'label' => 'events.participants.first_name.label',
                    'translation_domain' => 'events'
                ))->add('last_name', 'text', array(
                    'label' => 'events.participants.last_name.label',
                    'translation_domain' => 'events'
                ))->add('phone', 'text', array(
                    'label' => 'events.participants.phone.label',
                    'translation_domain' => 'events',
                    'required' => false
                ))->add('has_accepted', 'choice', array(
                    'choices' => array(
                        '1' => $this->get('translator')->trans('events.hasaccepted.yes', array(), 'events'),
                        '0' => $this->get('translator')->trans('events.hasaccepted.no', array(), 'events')
                    ),
                    'data' => $accept,
                    'multiple' => false,
                    'expanded' => false,
                    'label' => 'events.participants.has_accepted.label',
                    'translation_domain' => 'events',
                    'required' => false
                ))
            ;

            if ($this->container->get('sms_manager')->isEventRemindersEnabled()) {
                $form_rsvp->add('notify_by_sms', 'checkbox', array(
                    'label' => 'events.participants.notify_by_sms.label',
                    'translation_domain' => 'events',
                    'required' => false
                ));
            }

            $form_rsvp = $form_rsvp->getForm();

            $request = $this->getRequest();
            if ('POST' === $request->getMethod()) {
                $form_rsvp->bind($request);

                if ($form_rsvp->isValid()) {
                    $events_participant->setRespondedAt(time());
                    $events_participant->save();

                    $this->get('session')->getFlashBag()->add('notice', 'events.participant.rsvp.success');
                }
            }
            $form_rsvp = $form_rsvp->createView();
        }

        return $this->render('EventsBundle:Events:rsvp.html.twig', array(
            'page_type'          => 'event',
            'key'                => $key,
            'event'              => $event,
            'form_rsvp'          => $form_rsvp,
            'form_tell_a_friend' => $form_tell_a_friend
        ));
    }

    public function tellAFriendAction($key)
    {
        $friend = EventsParticipantsQuery::create()
            ->filterByTellAFriend(true)
            ->findOneByKey($key)
        ;

        if ($friend instanceof EventsParticipants) {
            $event = EventsQuery::create()
                ->findOneById($friend->getEventsId())
            ;
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
                        'translation_domain' => 'events',
                        'required' => false
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
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $events_participant->setKey(sha1(time()));
                    $events_participant->setEventsId($friend->getEventsId());
                    $events_participant->setInvitedBy($friend->getId());
                    $events_participant->save();

                    // Now send out some emails!
                    if ($events_participant->getEmail()) {
                        $mailer = $this->get('mail_manager');

                        $mailer->setMessage('events.participant.invited', array(
                            'event_date'       => $event->getEventDate('d/m'),
                            'event_time'       => $event->getEventDate('H:i'),
                            'to_name'          => $events_participant->getFirstName(). ' ' .$events_participant->getLastName(),
                            'hostess'          => $event->getHost(),
                            'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                            'zip'              => $event->getPostalCode(),
                            'city'             => $event->getCity(),
                            'email'            => $event->getEmail(),
                            'phone'            => $event->getPhone(),
                            'link'             => $this->generateUrl('events_rsvp', array('key' => $events_participant->getKey()), true),
                            'consultant_name'  => $event->getCustomersRelatedByConsultantsId()->getFirstName(). ' ' .$event->getCustomersRelatedByConsultantsId()->getLastName(),
                            'consultant_email' => $event->getCustomersRelatedByConsultantsId()->getEmail()
                        ));

                        $mailer->setTo(
                            $events_participant->getEmail(),
                            $events_participant->getFirstName(). ' ' .$events_participant->getLastName()
                        );

                        $name = $friend->getFirstName(). ' ' .$friend->getLastName();
                        $mailer->setFrom(array('events@pompdelux.com' => $name . ' (via POMPdeLUX)'));
                        $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                        $mailer->setReplyTo($friend->getEmail(), $name);
                        $mailer->send();
                    }

                    // Make sure that the friend only invites one
                    $friend->setTellAFriend(false);
                    $friend->save();

                    $this->get('session')->getFlashBag()->add('notice', 'events.participant.invited');
                    return $this->redirect($this->generateUrl('events_rsvp', array('key' => $key)));
                }
            }
        }

        $this->get('session')->getFlashBag()->add('notice', 'events.participant.invite.failed');
        return $this->redirect($this->generateUrl('events_rsvp', array('key' => $key)));
    }


    public function listAction()
    {
        $customer = CustomersPeer::getCurrent();
        $events = EventsQuery::create()
            ->filterByEventDate(array('min' => date('Y-m-d H:i:s')))
            ->filterByCustomersId($customer->getId())
            ->filterByIsOpen(true)
            ->find()
        ;

        return $this->render('EventsBundle:Events:list.html.twig', array(
            'events' => $events,
        ));
    }


    public function removeParticipantAction($event_id, $participant_id)
    {
        EventsParticipantsQuery::create()
            ->filterByEventsId($event_id)
            ->filterById($participant_id)
            ->findOne()
            ->delete()
        ;

        return $this->json_response(array(
            'status' => true,
            'message' => ''
        ));
    }

    public function selectEventAction()
    {
        $customer = CustomersPeer::getCurrent();
        $events = EventsQuery::create()
            ->filterByConsultantsId($customer->getId())
            ->orderByEventDate(\Criteria::ASC)
            ->filterByIsOpen(true)
            ->find()
        ;

        return $this->render('EventsBundle:Events:selectEvent.html.twig', array(
            'events' => $events,
            'continue_shopping' => $this->get('router')->generate('QuickOrderBundle_homepage'),
            'disable_discounts' => (bool) Hanzo::getInstance()->get('webshop.disable_discounts', 0),
        ));
    }

    public function setOrderTypeAction(Request $request)
    {
        $order = OrdersPeer::getCurrent();
        if ($order instanceof Orders) {
            // make sure the order "head" is reset before proceeding.
            $order->setEventsId(null);
            $order->setCustomersId(null);
            $order->setFirstName(null);
            $order->setLastName(null);
            $order->clearBillingAddress();
            $order->clearDeliveryAddress();

            Propel::setForceMasterConnection(TRUE);

            // remove any discount lines if changing event
            OrdersLinesQuery::create()
                ->filterByOrdersId($order->getId())
                ->filterByType('discount')
                ->find()
                ->delete();
            ;

            list($id, $code) = explode(':', $request->get('type'));

            $id   = trim($id);
            $code = trim($code);
            $goto = 'events_create_customer';

            if ($id == 'x') {
                $order->setAttribute('type', 'purchase', $code);

                if (in_array($code, array('private'))) {
                    $goto = '_checkout';
                }
            } else {
                $order->clearAttributesByNS('purchase');
                $order->setEventsId($id);
            }

            $hostess = $request->get('hostess');
            if (empty($hostess)) {
                $order->clearAttributesByKey('is_hostess_order');
            } else {
                $order->setAttribute('is_hostess_order', 'event', true);
            }

            $order->save();
        }

        return $this->redirect($this->generateUrl($goto));
    }

    public function myEventsAction()
    {
        $customer = CustomersPeer::getCurrent();
        $events = EventsQuery::create()
            ->joinCustomersRelatedByConsultantsId()
            ->filterByEventDate(array('min' => date('Y-m-d H:i:s')))
            ->filterByCustomersId($customer->getId())
            ->orderByEventDate()
            ->find()
        ;
        $myEvents = array();

        // Generate forms and form views for all events,
        // store them and the event data in the master events array myEvents.
        // Multiple forms on one page, requires a Form object to each.
        foreach ($events as $event) {
            if ($event instanceof Events) {
                $myEvents[$event->getId()] = array(
                    'data' => $event,
                    'form' => $this->createFormBuilder()
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
                                'translation_domain' => 'events',
                                'required' => false,
                                'error_bubbling' => true
                            )
                        )->add('phone', 'text',
                            array(
                                'label' => 'events.participants.phone.label',
                                'translation_domain' => 'events',
                                'required' => false,
                                'error_bubbling' => true
                            )
                        )->add('tell_a_friend', 'checkbox',
                            array(
                                'label' => 'events.participants.tell_a_friend.label',
                                'translation_domain' => 'events',
                                'required' => false
                            )
                        )->add('event_id', 'hidden', array('data' => $event->getId()))
                        ->getForm()
                );
                $myEvents[$event->getId()]['form_view'] = $myEvents[$event->getId()]['form']->createView();
            }
        }

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {

            $form = &$myEvents[$request->request->get('form')['event_id']]['form']; // Get the correct form instance for the given event. The eventid is sent with a hidden field
            $form->handleRequest($request);

            $data = $form->getData();

            if (!(empty($data['email']) && empty($data['phone'])) && $form->isValid()) {

                $event = $myEvents[$data['event_id']]['data'];

                $query = EventsParticipantsQuery::create()
                    ->filterByEventsId($data['event_id'])
                ;
                if (!empty($data['email'])) {
                    $query->filterByEmail($data['email']);
                }
                if (!empty($data['phone']) && empty($data['email'])) {
                    $query->filterByPhone($data['phone']);
                }
                $events_participant = $query->findOne();

                if(!$events_participant instanceof EventsParticipants){
                    $events_participant = new EventsParticipants();
                    $events_participant->setKey(sha1(time()))
                                       ->setEventsId($data['event_id']);

                }
                $events_participant->setInvitedBy($customer->getId())
                    ->setEmail($data['email'])
                    ->setPhone($data['phone'])
                    ->setFirstName($data['first_name'])
                    ->setLastName($data['last_name'])
                    ->setTellAFriend($data['tell_a_friend'])
                    ->save();

                // Now send out some emails!
                if ($events_participant->getEmail()) {
                    $mailer = $this->get('mail_manager');

                    $mailer->setMessage('events.participant.invited', array(
                        'event_date'       => $event->getEventDate('d/m'),
                        'event_time'       => $event->getEventDate('H:i'),
                        'to_name'          => $events_participant->getFirstName(). ' ' .$events_participant->getLastName(),
                        'hostess'          => $event->getHost(),
                        'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                        'zip'              => $event->getPostalCode(),
                        'city'             => $event->getCity(),
                        'email'            => $event->getEmail(),
                        'phone'            => $event->getPhone(),
                        'link'             => $this->generateUrl('events_rsvp', array('key' => $events_participant->getKey()), true),
                        'consultant_name'  => $myEvents[$data['event_id']]['data']->getCustomersRelatedByConsultantsId()->getFirstName(). ' ' .$myEvents[$data['event_id']]['data']->getCustomersRelatedByConsultantsId()->getLastName(),
                        'consultant_email' => $myEvents[$data['event_id']]['data']->getCustomersRelatedByConsultantsId()->getEmail()
                    ));

                    $mailer->setTo(
                        $events_participant->getEmail(),
                        $events_participant->getFirstName(). ' ' .$events_participant->getLastName()
                    );

                    $mailer->setFrom(array('events@pompdelux.com' => $event->getHost(). ' (via POMPdeLUX)'));
                    $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                    $mailer->setReplyTo($event->getEmail(), $event->getHost());
                    $mailer->send();
                }

                if ($events_participant->getPhone()) {
                    $this->get('sms_manager')->sendEventInvite($events_participant);
                }

                $this->get('session')->getFlashBag()->add('notice', 'events.participant.invited');
            }
        }

        // Store all participants in the master events array.
        foreach ($events as $event) {
            if ($event instanceof Events) {
                $events_participants = EventsParticipantsQuery::create()->findByEventsId($event->getId());
                $myEvents[$event->getId()]['participants'] = $events_participants;
            }
        }

        return $this->render('EventsBundle:Events:myEvents.html.twig', array(
            'page_type'     => 'event',
            'events'        => $myEvents
        ));
    }
}
