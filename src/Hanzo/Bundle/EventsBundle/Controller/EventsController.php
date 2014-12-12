<?php

namespace Hanzo\Bundle\EventsBundle\Controller;


use Hanzo\Bundle\EventsBundle\Form\Type\EventsType;
use Hanzo\Bundle\EventsBundle\Helpers\EventHostess;
use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\Events;
use Hanzo\Model\EventsParticipantsQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Customers;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersLinesQuery;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use Propel;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EventsController
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class EventsController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_CONSULTANT") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $request->getLocale()]));
        }

        $dateFilter['max'] = date('Y-m-d H:i:s');
        $archivedEvents = EventsQuery::create()
            ->filterByEventDate($dateFilter)
            ->filterByConsultantsId($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
            ->orderByEventDate('DESC')
            ->find();

        return $this->render('EventsBundle:Events:index.html.twig', [
            'page_type'       => 'calendar',
            'archived_events' => $archivedEvents
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEventsAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_CONSULTANT") or hasRole("ROLE_ADMIN")'))) {
            throw new AccessDeniedException();
        }

        $start = $request->query->get('start', null);
        $end   = $request->query->get('end', null);

        $dateFilter['min'] = gmdate("Y-m-d H:i:s", $start);
        $dateFilter['max'] = gmdate("Y-m-d H:i:s", $end);

        $events = EventsQuery::create()
            ->filterByEventDate($dateFilter)
            ->filterByConsultantsId($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
            ->find();

        $eventsArray = [];

        foreach ($events as $event) {

            $color = 'red';
            if (1 == $event->getIsOpen()) {
                $color = 'green';
                if ('HUS' == strtoupper($event->getType())) {
                    $color = 'blue';
                }
            }

            $eventsArray[] = [
                'id'        => $event->getId(),
                'title'     => $event->getCode(),
                'allDay'    => false,
                'start'     => $event->getEventDate('c'),
                'url'       => $this->get('router')->generate('events_view', ['id' => $event->getId()]),
                'className' => $event->getType(),
                'editable'  => false,
                'color'     => $color,
            ];
        }

        // Returns directly to the fullCalendar jQuery plugin
        if ($this->getFormat() == 'json') {
            return $this->json_response($eventsArray);
        }
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function viewAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_CONSULTANT") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $request->getLocale()]));
        }

        $event = EventsQuery::create()->findPK($id);

        $eventsParticipants = EventsParticipantsQuery::create()->findByEventsId($event->getId());

        return $this->render('EventsBundle:Events:view.html.twig', [
            'page_type'    => 'calendar',
            'event'        => $event,
            'participants' => $eventsParticipants,
            'id'           => $id
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function createAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_CONSULTANT") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $request->getLocale()]));
        }

        $hanzo = Hanzo::getInstance();

        $event = null;
        if ($id) {
            $event = EventsQuery::create()->findPK($id);

            // no editing old events
            if ($event->getEventDate('U') < time()) {
                $this->get('session')->getFlashBag()->add('notice', 'event.too.old.to.edit');

                return $this->redirect($this->generateUrl('events_index'));
            }
        } else {
            $event = new Events();
        }

        $form = $this->createForm(new EventsType(), $event);
        $form->get('event_date')->setData($event->getEventDate('m/d/Y H:i'));

        if ('POST' === $request->getMethod()) {
            $changed = !$event->isNew();
            if ($changed) {
                $oldEvent = $event->copy();
            }

            $form->handleRequest($request);

            if ('HUS' === $event->getType()) {
                if ('' == $event->getEventEndTime()) {
                    $form->get('event_end_time')->addError(new FormError($this->container->get('translator')->trans('events.missing.event_end_time', [], 'events')));
                }
                if ('' == $event->getRsvpType()) {
                    $form->get('rsvp_type')->addError(new FormError($this->container->get('translator')->trans('events.missing.rsvp_type', [], 'events')));
                }
            } else {
                $event->setEventEndTime(null);
                $event->setRsvpType(null);
                $event->setPublicNote(null);
            }

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
                if (!$changed) {
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
                        $mailer->sendParticipantEventChangesEmail();
                    } else {
                        $oldDate = new \DateTime($oldEvent->getEventDate());
                        $mailer->sendDateChangedHostessEmail($oldDate);
                        $mailer->sendDateChangedParticipantEmail($oldDate);
                    }
                } else {
                    $mailer->sendHostessEmail();
                }

                $this->get('session')->getFlashBag()->add('notice', 'events.created');

                return $this->redirect($this->generateUrl('events_index'));
            }
        }

        return $this->render('EventsBundle:Events:create.html.twig', [
            'page_type' => 'calendar',
            'form'      => $form->createView(),
            'id'        => $id
        ]);
    }

    /**
     * @param string $email
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCustomerAction($email)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_CONSULTANT") or hasRole("ROLE_ADMIN")'))) {
            throw new AccessDeniedException();
        }

        $customer = CustomersQuery::create()->findOneByEmail(str_replace(' ', '+', $email));

        if ($customer instanceof Customers) {
            $c = new \Criteria();
            $c->add(AddressesPeer::TYPE, 'payment');
            $address = $customer->getAddressess()->getFirst();

            if ($this->getFormat() == 'json') {
                return $this->json_response([
                    'status'  => true,
                    'message' => $this->get('translator')->trans('events.customer.found', [], 'events'),
                    'data'    => [
                        'id'      => $customer->getId(),
                        'name'    => $customer->getFirstName() . ' ' . $customer->getLastName(),
                        'phone'   => $customer->getPhone(),
                        'email'   => $customer->getEmail(),
                        'address' => $address->getAddressLine1(),
                        'zip'     => $address->getPostalCode(),
                        'city'    => $address->getCity(),
                    ],
                ]);
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => false,
                'message' => $this->get('translator')->trans('events.customer.notfound', [], 'events')
            ]);
        }
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @throws \PropelException
     */
    public function closeAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_CONSULTANT") or hasRole("ROLE_ADMIN")'))) {
            throw new AccessDeniedException();
        }

        $event = EventsQuery::create()->findPK($id);
        if ($event instanceof Events) {
            $event->setIsOpen(false);
            $event->save();
        }

        $request->getSession()->getFlashBag()->add('notice', $this->get('translator')->trans('event.closed', [], 'events'));

        return $this->redirect($this->generateUrl('events_index'));
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @throws \Exception
     * @throws \PropelException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_CONSULTANT") or hasRole("ROLE_ADMIN")'))) {
            throw new AccessDeniedException();
        }

        $event = EventsQuery::create()->findPK($id);
        if ($event instanceof Events) {

            // no deleting old events
            if ($event->getEventDate('U') < time()) {
                $this->get('session')->getFlashBag()->add('notice', 'event.too.old.to.delete');

                return $this->redirect($this->generateUrl('events_index'));
            }

            $consultant = CustomersQuery::create()->joinWithConsultants()->findPK($event->getConsultantsId());
            // Send some emails for the host and participants
            $participants = EventsParticipantsQuery::create()
                ->filterByEventsId($event->getId())
                ->find();

            // Now send out some emails!
            $mailer = $this->get('hanzo.event.mailer');
            $mailer->setEventData($event, null, $consultant);
            $mailer->sendHostessDeletedEventEmail();

            foreach ($participants as $participant) {
                if (!$participant->getEmail()) {
                    continue;
                }

                $mailer->sendParticipantDeletedEventEmail($participant);
            }

            $event->delete();
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => true,
                'message' => $this->container->get('translator')->trans('events.delete.success', [], 'events')
            ]);
        }

        $request->getSession()->getFlashBag()->add('notice', 'events.delete.success');

        return $this->redirect($this->generateUrl('events_index'));
    }

    /**
     * @param Request $request
     * @param string  $key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function inviteAction(Request $request, $key)
    {
        $customer = CustomersPeer::getCurrent();
        $event    = EventsQuery::create()
            ->filterByEventDate(['min' => date('Y-m-d H:i:s')])
            ->filterByCustomersId($customer->getId())
            ->findOneByKey($key);

        $eventsParticipant = null;
        $form              = null;

        if ($event instanceof Events) {
            $eventsParticipant = new EventsParticipants();
            $form              = $this->createForm('events_participant', $eventsParticipant);

            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);

                if ($eventsParticipant->getEmail()) {
                    $res = EventsParticipantsQuery::create()
                        ->filterByEventsId($event->getId())
                        ->findByEmail($eventsParticipant->getEmail());

                    if ($res->count()) {
                        $error = new FormError($this->container->get('translator')->trans('event.email.exists', [], 'events'));
                        $form->addError($error);
                    }
                }

                if ($form->isValid()) {
                    $eventsParticipant->setKey(sha1(time()));
                    $eventsParticipant->setEventsId($event->getId());
                    $eventsParticipant->save();

                    // Now send out some emails!
                    if ($eventsParticipant->getEmail()) {
                        $mailer = $this->container->get('hanzo.event.mailer');
                        $mailer->setEventData($event);
                        $mailer->sendParticipantEventInviteEmail($eventsParticipant);
                    }

                    if ($eventsParticipant->getPhone()) {
                        $this->container->get('sms_manager')->sendEventInvite($eventsParticipant);
                    }

                    $request->getSession()->getFlashBag()->add('notice', 'events.participant.invited');
                }
            }

            $form              = $form->createView();
            $eventsParticipant = EventsParticipantsQuery::create()->findByEventsId($event->getId());
        }

        return $this->render('EventsBundle:Events:invite.html.twig', [
            'page_type'    => 'event',
            'key'          => $key,
            'event'        => $event,
            'form'         => $form,
            'participants' => $eventsParticipant
        ]);
    }

    /**
     * @param Request $request
     * @param string  $key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rsvpAction(Request $request, $key)
    {
        $event = null;
        $eventParticipant = EventsParticipantsQuery::create()->findOneByKey($key);

        if ($eventParticipant instanceof EventsParticipants) {
            $event = EventsQuery::create()
                ->filterByEventDate(['min' => date('Y-m-d H:i:s')])
                ->findOneById($eventParticipant->getEventsId());
        }

        $formRsvp = null;
        $formTellAFriend = null;

        if ($eventParticipant instanceof EventsParticipants && $event instanceof Events) {
            if (true === $eventParticipant->getTellAFriend()) {
                $formTellAFriend = $this->createForm('events_tell_a_friend')->createView();
            }

            $formRsvp = $this->createForm('events_rsvp', $eventParticipant);

            if ($this->container->get('sms_manager')->isEventRemindersEnabled()) {
                $formRsvp->add('notify_by_sms', 'checkbox', [
                    'label'              => 'events.participants.notify_by_sms.label',
                    'translation_domain' => 'events',
                    'required'           => false
                ]);
            }

            if ('POST' === $request->getMethod()) {
                $formRsvp->handleRequest($request);

                if ($formRsvp->isValid()) {
                    $eventParticipant->setHasAccepted($formRsvp->get('has_accepted'));
                    $eventParticipant->setRespondedAt(time());
                    $eventParticipant->save();

                    $request->getSession()->getFlashBag()->add('notice', 'events.participant.rsvp.success');
                }
            }

            $formRsvp = $formRsvp->createView();
        }

        return $this->render('EventsBundle:Events:rsvp.html.twig', [
            'page_type'          => 'event',
            'key'                => $key,
            'event'              => $event,
            'form_rsvp'          => $formRsvp,
            'form_tell_a_friend' => $formTellAFriend
        ]);
    }

    /**
     * @param Request $request
     * @param string  $key
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @throws \PropelException
     */
    public function tellAFriendAction(Request $request, $key)
    {
        $friend = EventsParticipantsQuery::create()
            ->filterByTellAFriend(true)
            ->findOneByKey($key);

        if ($friend instanceof EventsParticipants) {
            $event = EventsQuery::create()
                ->findOneById($friend->getEventsId());

            $eventParticipant = new EventsParticipants();
            $form = $this->createForm('events_tell_a_friend', $eventParticipant);

            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $eventParticipant->setKey(sha1(time()));
                    $eventParticipant->setEventsId($friend->getEventsId());
                    $eventParticipant->setInvitedBy($friend->getId());
                    $eventParticipant->save();

                    // Now send out some emails!
                    if ($eventParticipant->getEmail()) {
                        $mailer = $this->get('hanzo.event.mailer');
                        $mailer->setEventData($event, null, $event->getCustomersRelatedByConsultantsId());
                        $mailer->sendParticipantEventInviteEmail($eventParticipant);
                    }

                    // Make sure that the friend only invites one
                    $friend->setTellAFriend(false);
                    $friend->save();

                    $this->get('session')->getFlashBag()->add('notice', 'events.participant.invited');

                    return $this->redirect($this->generateUrl('events_rsvp', ['key' => $key]));
                }
            }
        }

        $this->get('session')->getFlashBag()->add('notice', 'events.participant.invite.failed');

        return $this->redirect($this->generateUrl('events_rsvp', ['key' => $key]));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $customer = CustomersPeer::getCurrent();
        $events = EventsQuery::create()
            ->filterByEventDate(['min' => date('Y-m-d H:i:s')])
            ->filterByCustomersId($customer->getId())
            ->filterByIsOpen(true)
            ->find();

        return $this->render('EventsBundle:Events:list.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @param int $event_id
     * @param int $participant_id
     *
     * @throws \Exception
     * @throws \PropelException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeParticipantAction($event_id, $participant_id)
    {
        EventsParticipantsQuery::create()
            ->filterByEventsId($event_id)
            ->filterById($participant_id)
            ->findOne()
            ->delete();

        return $this->json_response([
            'status'  => true,
            'message' => ''
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function selectEventAction()
    {
        $customer = CustomersPeer::getCurrent();
        $events = EventsQuery::create()
            ->filterByConsultantsId($customer->getId())
            ->orderByEventDate(\Criteria::ASC)
            ->filterByIsOpen(true)
            ->find();

        return $this->render('EventsBundle:Events:selectEvent.html.twig', [
            'events'            => $events,
            'continue_shopping' => $this->get('router')->generate('QuickOrderBundle_homepage'),
            'disable_discounts' => (bool) Hanzo::getInstance()->get('webshop.disable_discounts', 0),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @throws \PropelException
     */
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

            \Propel::setForceMasterConnection(true);

            // remove any discount lines if changing event
            OrdersLinesQuery::create()
                ->filterByOrdersId($order->getId())
                ->filterByType('discount')
                ->find()
                ->delete();

            list($id, $code) = explode(':', $request->get('type'));

            $id   = trim($id);
            $code = trim($code);
            $goto = 'events_create_customer';

            if ($id == 'x') {
                $order->setAttribute('type', 'purchase', $code);

                if (in_array($code, ['private'])) {
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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function myEventsAction(Request $request)
    {
        $customer = CustomersPeer::getCurrent();
        $events   = EventsQuery::create()
            ->joinCustomersRelatedByConsultantsId()
            ->filterByEventDate(['min' => date('Y-m-d H:i:s')])
            ->filterByCustomersId($customer->getId())
            ->orderByEventDate()
            ->find();

        $myEvents = [];

        // Generate forms and form views for all events,
        // store them and the event data in the master events array myEvents.
        // Multiple forms on one page, requires a Form object to each.
        foreach ($events as $event) {
            if (!$event instanceof Events) {
                continue;
            }

            $participant = new EventsParticipants();
            $participant->setEventsId($event->getId());

            $myEvents[$event->getId()] = [
                'data' => $event,
                'form' => $this->createForm('events_participant', $participant)->createView(),
            ];
        }

        if ('POST' === $request->getMethod()) {
            $form = $this->createForm('events_participant');
            $form->handleRequest($request);

            /** @var \Hanzo\Model\EventsParticipants $participant */
            $participant = $form->getData();

            if ($form->isValid()) {
                $query = EventsParticipantsQuery::create()
                    ->filterByEventsId($participant->getEventsId());

                // the query is only valid as long as there is either an email or a phone number.
                $validQuery = false;
                if ($participant->getEmail()) {
                    $query->filterByEmail($participant->getEmail());
                    $validQuery = true;
                }
                if ($participant->getPhone()) {
                    $query->filterByPhone($participant->getPhone());
                    $validQuery = true;
                }

                if (true === $validQuery) {
                    $eventParticipant = $query->findOne();
                }

                if (isset($eventParticipant) && ($eventParticipant instanceof EventsParticipants)) {
                    $this->container->get('session')->getFlashBag()->add('notice', 'events.participant.exists');

                    return $this->redirect($this->generateUrl('events_my_events'));
                }

                $participant
                    ->setInvitedBy($customer->getId())
                    ->setKey(sha1(time()))
                    ->save();

                $event = $myEvents[$participant->getEventsId()]['data'];

                // Now send out some emails!
                if ($participant->getEmail()) {
                    $mailer = $this->get('hanzo.event.mailer');
                    $mailer->setEventData($event, null, $myEvents[$participant->getEventsId()]['data']->getCustomersRelatedByConsultantsId());
                    $mailer->sendParticipantEventInviteEmail($participant);
                }

                if ($participant->getPhone()) {
                    $this->get('sms_manager')->sendEventInvite($participant);
                }

                $this->get('session')->getFlashBag()->add('notice', 'events.participant.invited');

                return $this->redirect($this->generateUrl('events_my_events'));
            }
        }

        // Store all participants in the master events array.
        foreach ($events as $event) {
            $myEvents[$event->getId()]['participants'] = EventsParticipantsQuery::create()->findByEventsId($event->getId());
        }

        return $this->render('EventsBundle:Events:myEvents.html.twig', [
            'page_type' => 'event',
            'events'    => $myEvents
        ]);
    }
}
