<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Hanzo\Model\WishlistsQuery;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

use Propel;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\Events;
use Hanzo\Model\EventsParticipantsQuery;
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Customers;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\Addresses;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\CountriesQuery;

/**
 * Class EventsController
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class EventsController extends CoreController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction()
    {
        $hanzo = Hanzo::getInstance();
        if ((false === $this->get('security.context')->isGranted('ROLE_CONSULTANT')) &&
            (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
        ) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $hanzo->get('core.locale')]));
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
        if (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT') &&
            false === $this->get('security.context')->isGranted('ROLE_ADMIN')
        ) {
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
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function viewAction($id)
    {
        $hanzo = Hanzo::getInstance();
        if ((false === $this->get('security.context')->isGranted('ROLE_CONSULTANT')) &&
            (false === $this->get('security.context')->isGranted('ROLE_ADMIN')
        )) {
            return $this->redirect($this->generateUrl('login', [
                '_locale' => $hanzo->get('core.locale')
            ]));
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
        $hanzo = Hanzo::getInstance();
        if ((false === $this->get('security.context')->isGranted('ROLE_CONSULTANT')) &&
            (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
        ) {
            return $this->redirect($this->generateUrl('login', [
                '_locale' => $hanzo->get('core.locale')
            ]));
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

        $form = $this->createFormBuilder($event, ['translation_domain' => 'events'])
            ->add('customers_id', 'hidden')
            ->add('event_date', 'text', [
                'attr'               => ['class' => 'datetimepicker'],
                'label'              => 'events.event_date.label',
                'translation_domain' => 'events',
                'data'               => $event->getEventDate('m/d/Y H:i')
            ])->add('host', 'text', [
                'label'              => 'events.host.label',
                'translation_domain' => 'events',
            ])->add('address_line_1', 'text', [
                'label'              => 'events.address_line_1.label',
                'translation_domain' => 'events',
            ])->add('postal_code', 'text', [
                'label'              => 'events.postal_code.label',
                'translation_domain' => 'events',
            ])->add('city', 'text', [
                'label'              => 'events.city.label',
                'translation_domain' => 'events',
            ])->add('phone', 'text', [
                'label'              => 'events.phone.label',
                'translation_domain' => 'events',
            ])->add('email', 'text', [
                'label'              => 'events.email.label',
                'translation_domain' => 'events',
            ])->add('description', 'textarea', [
                'label'              => 'events.description.label',
                'translation_domain' => 'events',
                'required'           => false
            ])->add('type', 'choice', [
                'choices'            => [
                    'AR'  => 'events.type.choice.ar',
                    'HUS' => 'events.type.choice.hus',
                ],
                'label'              => 'events.type.label',
                'translation_domain' => 'events'
            ])->add('notify_hostess', 'checkbox', [
                'label'              => 'events.notify_hostess.label',
                'translation_domain' => 'events',
                'required'           => false
            ])->getForm();

        if ('POST' === $request->getMethod()) {

            $changed = isset($id) ? true : false; // Keep track of which this is a new event or an old event
            if ($changed) {
                $oldEvent = $event->copy(); // Keep a copy of the old data before we bind the request
            }

            $form->handleRequest($request);

            if ($form->isValid()) {
                $consultant = CustomersPeer::getCurrent();

                $customersId = $event->getCustomersId(); // from the form
                $host = null; // Customers Object
                $changedHost = false; // Bool wheter the host has changed
                $newHost = false; // Bool wheter a new Customers have been created

                // Hvis der er ændret i email = ny host
                if ($changed && ($oldEvent->getEmail() != $event->getEmail())) {
                    $changedHost = true; // Keep track if the host is new/changed
                }

                $host = CustomersQuery::create()
                    ->findOneByEmail($event->getEmail());

                // Der er ikke tilknyttet nogle Customers som vært, opret en ny
                if (!($host instanceof Customers)) {
                    @list($first, $last) = explode(' ', $event->getHost(), 2);

                    $newHost = true;
                    $host = new Customers();
                    $host->setPasswordClear($event->getPhone());
                    $host->setPassword(sha1($event->getPhone()));
                    $host->setPhone($event->getPhone());
                    $host->setEmail($event->getEmail());
                    $host->setFirstName($first);
                    $host->setLastName($last);

                    try {
                        $host->save();

                        $country = CountriesQuery::create()->findOneByIso2($hanzo->get('core.country'));

                        // create customer payment address
                        $address = new Addresses();
                        $address->setCustomersId($host->getId());
                        $address->setFirstName($first);
                        $address->setLastName($last);
                        $address->setAddressLine1($event->getAddressLine1());
                        $address->setPostalCode($event->getPostalCode());
                        $address->setCity($event->getCity());
                        $address->setCountry($country->getName());
                        $address->setCountriesId($country->getId());
                        $address->save();

                    } catch (\PropelException $e) {
                        Tools::log($event->toArray());
                    }
                }

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

                $mailer = $this->get('mail_manager');
                if ($changed) {
                    // If the event has changed and its a new host, send specific mails to all
                    if ($changedHost) {
                        if ($event->getNotifyHostess()) {

                            // Send an email to the old Host
                            $mailer->setMessage('events.hostess.eventmovedfrom', [
                                'event_date'       => $event->getEventDate('d/m'),
                                'event_time'       => $event->getEventDate('H:i'),
                                'name'             => $oldEvent->getHost(),
                                'from_address'     => $oldEvent->getAddressLine1(). ' ' .$oldEvent->getAddressLine2(),
                                'from_zip'         => $oldEvent->getPostalCode(),
                                'from_city'        => $oldEvent->getCity(),
                                'to_name'          => $event->getHost(),
                                'to_address'       => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                                'to_zip'           => $event->getPostalCode(),
                                'to_city'          => $event->getCity(),
                                'to_phone'         => $event->getPhone(),
                                'link'             => $this->generateUrl('events_invite', ['key' => $event->getKey()], true),
                                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                                'consultant_email' => $consultant->getEmail(),
                            ]);
                            $mailer->setTo([$oldEvent->getEmail() => $oldEvent->getHost()]);
                            $mailer->setFrom([$consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()]);
                            $mailer->send();

                            // Send an email to the new Host
                            $mailer->setMessage('events.hostess.eventmovedto', [
                                'event_date'       => $event->getEventDate('d/m'),
                                'event_time'       => $event->getEventDate('H:i'),
                                'from_name'        => $oldEvent->getHost(),
                                'from_address'     => $oldEvent->getAddressLine1(). ' ' .$oldEvent->getAddressLine2(),
                                'from_zip'         => $oldEvent->getPostalCode(),
                                'from_city'        => $oldEvent->getCity(),
                                'from_phone'       => $oldEvent->getPhone(),
                                'name'             => $event->getHost(),
                                'to_address'       => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                                'to_zip'           => $event->getPostalCode(),
                                'to_city'          => $event->getCity(),
                                'email'            => $host->getEmail(),
                                'password'         => $host->getPasswordClear(),
                                'phone'            => $event->getPhone(),
                                'link'             => $this->generateUrl('events_invite', ['key' => $event->getKey()], true),
                                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                                'consultant_email' => $consultant->getEmail(),
                            ]);
                            $mailer->setTo([$event->getEmail() => $event->getHost()]);
                            $mailer->setFrom([$consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()]);
                            $mailer->send();
                        }

                        // Find all participants.
                        $participants = EventsParticipantsQuery::create()
                            ->filterByEventsId($event->getId())
                            ->find();

                        // Send an email to all participants
                        foreach ($participants as $participant) {
                            if (!$participant->getEmail()) {
                                continue;
                            }

                            $mailer->setMessage('events.participant.eventchanged', [
                                'event_date'       => $event->getEventDate('d/m'),
                                'event_time'       => $event->getEventDate('H:i'),
                                'to_name'          => $participant->getFirstName(). ' ' .$participant->getLastName(),
                                'hostess'          => $event->getHost(),
                                'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                                'zip'              => $event->getPostalCode(),
                                'city'             => $event->getCity(),
                                'phone'            => $event->getPhone(),
                                'email'            => $event->getEmail(),
                                'link'             => $this->generateUrl('events_rsvp', ['key' => $participant->getKey()], true),
                                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                                'consultant_email' => $consultant->getEmail(),
                            ]);

                            $mailer->setTo([$participant->getEmail() => $participant->getFirstName(). ' ' .$participant->getLastName()]);
                            $mailer->setFrom(['events@pompdelux.com' => $event->getHost() . ' (via POMPdeLUX)']);
                            $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                            $mailer->setReplyTo($event->getEmail(), $event->getHost());
                            $mailer->send();
                        }
                    }
                } else {
                    if ($event->getNotifyHostess()) {

                        // Send an email to the new Host
                        $mailer->setMessage('events.hostess.create', [
                            'event_date'       => $event->getEventDate('d/m'),
                            'event_time'       => $event->getEventDate('H:i'),
                            'to_name'          => $event->getHost(),
                            'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                            'zip'              => $event->getPostalCode(),
                            'city'             => $event->getCity(),
                            'email'            => $host->getEmail(),
                            'password'         => $host->getPasswordClear(),
                            'phone'            => $event->getPhone(),
                            'link'             => $this->generateUrl('events_invite', ['key' => $event->getKey()], true),
                            'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                            'consultant_email' => $consultant->getEmail()
                        ]);
                        $mailer->setTo([$event->getEmail() => $event->getHost()]);
                        $mailer->setFrom([$consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()]);
                        $mailer->send();
                    }
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
                return $this->json_response([
                    'status'  => true,
                    'message' => $this->get('translator')->trans('events.customer.found', [], 'events'),
                    'data' => [
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
        if ((false === $this->get('security.context')->isGranted('ROLE_CONSULTANT')) &&
            (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
        ) {
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
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function deleteAction($id)
    {
        if ((false === $this->get('security.context')->isGranted('ROLE_CONSULTANT')) &&
            (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
        ) {
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
            $mailer = $this->get('mail_manager');

            $mailer->setMessage('events.hostess.delete', [
                'event_date'       => $event->getEventDate('d/m'),
                'event_time'       => $event->getEventDate('H:i'),
                'to_name'          => $event->getHost(),
                'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                'zip'              => $event->getPostalCode(),
                'city'             => $event->getCity(),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ]);

            $mailer->setTo([$event->getEmail() => $event->getHost()]);
            $mailer->setFrom([$consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()]);
            $mailer->send();

            foreach ($participants as $participant) {
                if (!$participant->getEmail()) {
                    continue;
                }

                $mailer->setMessage('events.participants.delete', [
                    'event_date'    => $event->getEventDate('d/m'),
                    'event_time'    => $event->getEventDate('H:i'),
                    'to_name'       => $participant->getFirstName(),
                    'address'       => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                    'zip'           => $event->getPostalCode(),
                    'city'          => $event->getCity(),
                    'hostess'       => $event->getHost(),
                    'hostess_email' => $event->getEmail()
                ]);

                $mailer->setTo($participant->getEmail(), $participant->getFirstName(). ' ' .$participant->getLastName());
                $mailer->setFrom(['events@pompdelux.com' => $event->getHost() . ' (via POMPdeLUX)']);
                $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                $mailer->setReplyTo($event->getEmail(), $event->getHost());
                $mailer->send();
            }

            $event->delete();
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => true,
                'message' => $this->get('translator')->trans('events.delete.success', [], 'events')
            ]);
        }

        $this->get('session')->getFlashBag()->add('notice', 'events.delete.success');

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
        $event = EventsQuery::create()
            ->filterByEventDate(['min' => date('Y-m-d H:i:s')])
            ->filterByCustomersId($customer->getId())
            ->findOneByKey($key);

        $eventsParticipants = null;
        $form = null;

        if ($event instanceof Events) {
            $eventsParticipant = new EventsParticipants();

            $form = $this->createFormBuilder($eventsParticipant)
                ->add('first_name', 'text', [
                    'label'              => 'events.participants.first_name.label',
                    'translation_domain' => 'events'
                ])->add('last_name', 'text', [
                    'label'              => 'events.participants.last_name.label',
                    'translation_domain' => 'events'
                ])->add('email', 'email', [
                    'label'              => 'events.participants.email.label',
                    'required'           => false,
                    'translation_domain' => 'events'
                ])->add('phone', 'text', [
                    'label'              => 'events.participants.phone.label',
                    'translation_domain' => 'events',
                    'required'           => false,
                    'attr'               => ['class' => 'dk']
                ])->add('tell_a_friend', 'checkbox', [
                    'label'              => 'events.participants.tell_a_friend.label',
                    'translation_domain' => 'events',
                    'required'           => false
                ])->getForm();

            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);

                if ($eventsParticipant->getEmail()) {
                    $res = EventsParticipantsQuery::create()
                        ->filterByEventsId($event->getId())
                        ->findByEmail($eventsParticipant->getEmail());

                    if ($res->count()) {
                        $error = new FormError($this->get('translator')->trans('event.email.exists', [], 'events'));
                        $form->addError($error);
                    }
                }

                if ($form->isValid()) {
                    $eventsParticipant->setKey(sha1(time()));
                    $eventsParticipant->setEventsId($event->getId());
                    $eventsParticipant->save();

                    // Now send out some emails!
                    if ($eventsParticipant->getEmail()) {
                        $mailer = $this->get('mail_manager');

                        $mailer->setMessage('events.participant.invited', [
                            'event_date'       => $event->getEventDate('d/m'),
                            'event_time'       => $event->getEventDate('H:i'),
                            'to_name'          => $eventsParticipant->getFirstName(). ' ' .$eventsParticipant->getLastName(),
                            'hostess'          => $event->getHost(),
                            'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                            'zip'              => $event->getPostalCode(),
                            'city'             => $event->getCity(),
                            'email'            => $event->getEmail(),
                            'phone'            => $event->getPhone(),
                            'link'             => $this->generateUrl('events_rsvp', ['key' => $eventsParticipant->getKey()], true)
                        ]);

                        $mailer->setTo(
                            $eventsParticipant->getEmail(),
                            $eventsParticipant->getFirstName(). ' ' .$eventsParticipant->getLastName()
                        );
                        $mailer->setFrom(['events@pompdelux.com' => $event->getHost(). ' (via POMPdeLUX)']);
                        $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                        $mailer->setReplyTo($event->getEmail(), $event->getHost());
                        $mailer->send();
                    }

                    if ($eventsParticipant->getPhone()) {
                        $this->get('sms_manager')->sendEventInvite($eventsParticipant);
                    }

                    $this->get('session')->getFlashBag()->add('notice', 'events.participant.invited');
                }
            }

            $form = $form->createView();

            $eventsParticipants = EventsParticipantsQuery::create()->findByEventsId($event->getId());
        }

        return $this->render('EventsBundle:Events:invite.html.twig', [
            'page_type'    => 'event',
            'key'          => $key,
            'event'        => $event,
            'form'         => $form,
            'participants' => $eventsParticipants
        ]);
    }

    /**
     * @param Request $request
     * @param string  $key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function rsvpAction(Request $request, $key)
    {
        $eventsParticipant = EventsParticipantsQuery::create()->findOneByKey($key);
        $event = null;
        if ($eventsParticipant instanceof EventsParticipants) {
            $event = EventsQuery::create()
                ->filterByEventDate(['min' => date('Y-m-d H:i:s')])
                ->findOneById($eventsParticipant->getEventsId());
        }

        $formRsvp = null;
        $formTellAFriend = null;

        if ($eventsParticipant instanceof EventsParticipants && $event instanceof Events) {
            if (true === $eventsParticipant->getTellAFriend()) {
                $formTellAFriend = $this->createFormBuilder(new EventsParticipants())
                    ->add('first_name', 'text', [
                        'label'              => 'events.participants.first_name.label',
                        'translation_domain' => 'events'
                    ])->add('last_name', 'text', [
                        'label'              => 'events.participants.last_name.label',
                        'translation_domain' => 'events'
                    ])->add('email', 'email', [
                        'label'              => 'events.participants.email.label',
                        'translation_domain' => 'events',
                        'required'           => false
                    ])->add('phone', 'text', [
                        'label'              => 'events.participants.phone.label',
                        'translation_domain' => 'events',
                        'required'           => false
                    ])->getForm();

                $formTellAFriend = $formTellAFriend->createView();
            }

            $accept = 1;
            if ($eventsParticipant->getRespondedAt()) {
                $accept = $eventsParticipant->getHasAccepted();
            }

            $formRsvp = $this->createFormBuilder($eventsParticipant)
                ->add('first_name', 'text', [
                    'label'              => 'events.participants.first_name.label',
                    'translation_domain' => 'events'
                ])->add('last_name', 'text', [
                    'label'              => 'events.participants.last_name.label',
                    'translation_domain' => 'events'
                ])->add('phone', 'text', [
                    'label'              => 'events.participants.phone.label',
                    'translation_domain' => 'events',
                    'required'           => false
                ])->add('has_accepted', 'choice', [
                    'choices'            => [
                        '1' => $this->get('translator')->trans('events.hasaccepted.yes', [], 'events'),
                        '0' => $this->get('translator')->trans('events.hasaccepted.no', [], 'events')
                    ],
                    'data'               => $accept,
                    'multiple'           => false,
                    'expanded'           => false,
                    'label'              => 'events.participants.has_accepted.label',
                    'translation_domain' => 'events',
                    'required'           => false
                ]);

            if ($this->container->get('sms_manager')->isEventRemindersEnabled()) {
                $formRsvp->add('notify_by_sms', 'checkbox', [
                    'label'              => 'events.participants.notify_by_sms.label',
                    'translation_domain' => 'events',
                    'required'           => false
                ]);
            }

            $formRsvp = $formRsvp->getForm();
            if ('POST' === $request->getMethod()) {
                $formRsvp->bind($request);

                if ($formRsvp->isValid()) {
                    $eventsParticipant->setRespondedAt(time());
                    $eventsParticipant->save();

                    $this->get('session')->getFlashBag()->add('notice', 'events.participant.rsvp.success');
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

            $eventsParticipant = new EventsParticipants();

            $form = $this->createFormBuilder($eventsParticipant)
                ->add('first_name', 'text', [
                    'label'              => 'events.participants.first_name.label',
                    'translation_domain' => 'events'
                ])->add('last_name', 'text', [
                    'label'              => 'events.participants.last_name.label',
                    'translation_domain' => 'events'
                ])->add('email', 'email', [
                    'label'              => 'events.participants.email.label',
                    'translation_domain' => 'events',
                    'required'           => false
                ])->add('phone', 'text', [
                    'label'              => 'events.participants.phone.label',
                    'translation_domain' => 'events',
                    'required'           => false
                ])->getForm();

            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $eventsParticipant->setKey(sha1(time()));
                    $eventsParticipant->setEventsId($friend->getEventsId());
                    $eventsParticipant->setInvitedBy($friend->getId());
                    $eventsParticipant->save();

                    // Now send out some emails!
                    if ($eventsParticipant->getEmail()) {
                        $mailer = $this->get('mail_manager');

                        $mailer->setMessage('events.participant.invited', [
                            'event_date'       => $event->getEventDate('d/m'),
                            'event_time'       => $event->getEventDate('H:i'),
                            'to_name'          => $eventsParticipant->getFirstName(). ' ' .$eventsParticipant->getLastName(),
                            'hostess'          => $event->getHost(),
                            'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                            'zip'              => $event->getPostalCode(),
                            'city'             => $event->getCity(),
                            'email'            => $event->getEmail(),
                            'phone'            => $event->getPhone(),
                            'link'             => $this->generateUrl('events_rsvp', ['key' => $eventsParticipant->getKey()], true),
                            'consultant_name'  => $event->getCustomersRelatedByConsultantsId()->getFirstName(). ' ' .$event->getCustomersRelatedByConsultantsId()->getLastName(),
                            'consultant_email' => $event->getCustomersRelatedByConsultantsId()->getEmail()
                        ]);

                        $mailer->setTo(
                            $eventsParticipant->getEmail(),
                            $eventsParticipant->getFirstName(). ' ' .$eventsParticipant->getLastName()
                        );

                        $name = $friend->getFirstName(). ' ' .$friend->getLastName();
                        $mailer->setFrom(['events@pompdelux.com' => $name . ' (via POMPdeLUX)']);
                        $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                        $mailer->setReplyTo($friend->getEmail(), $name);
                        $mailer->send();
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
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

            Propel::setForceMasterConnection(true);

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

            $attributes = $order->getAttributes();

            if (isset($attributes->wishlist, $attributes->wishlist->id)) {
                $customersId = WishlistsQuery::create()
                    ->select('customers_id')
                    ->findOneById($attributes->wishlist->id);

                $order->setCustomersId($customersId);
                $goto = '_checkout';
            }

            $order->save();
        }

        return $this->redirect($this->generateUrl($goto));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function myEventsAction(Request $request)
    {
        $customer = CustomersPeer::getCurrent();
        $events = EventsQuery::create()
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
            if ($event instanceof Events) {
                $myEvents[$event->getId()] = [
                    'data' => $event,
                    'form' => $this->createFormBuilder()
                        ->add('first_name', 'text', [
                            'label'              => 'events.participants.first_name.label',
                            'translation_domain' => 'events'
                        ])->add('last_name', 'text', [
                            'label'              => 'events.participants.last_name.label',
                            'translation_domain' => 'events'
                        ])->add('email', 'email', [
                            'label'              => 'events.participants.email.label',
                            'translation_domain' => 'events',
                            'required'           => false,
                            'error_bubbling'     => true
                        ])->add('phone', 'text', [
                            'label'              => 'events.participants.phone.label',
                            'translation_domain' => 'events',
                            'required'           => false,
                            'error_bubbling'     => true
                        ])->add('tell_a_friend', 'checkbox', [
                            'label'              => 'events.participants.tell_a_friend.label',
                            'translation_domain' => 'events',
                            'required'           => false
                        ])->add('event_id', 'hidden', ['data' => $event->getId()])
                        ->getForm()
                ];

                $myEvents[$event->getId()]['form_view'] = $myEvents[$event->getId()]['form']->createView();
            }
        }

        if ('POST' === $request->getMethod()) {

            $form = &$myEvents[$request->request->get('form')['event_id']]['form']; // Get the correct form instance for the given event. The eventid is sent with a hidden field
            $form->handleRequest($request);

            $data = $form->getData();

            if (!(empty($data['email']) && empty($data['phone'])) && $form->isValid()) {

                $event = $myEvents[$data['event_id']]['data'];

                $query = EventsParticipantsQuery::create()
                    ->filterByEventsId($data['event_id']);

                if (!empty($data['email'])) {
                    $query->filterByEmail($data['email']);
                }
                if (!empty($data['phone']) && empty($data['email'])) {
                    $query->filterByPhone($data['phone']);
                }

                $eventsParticipant = $query->findOne();

                if (!$eventsParticipant instanceof EventsParticipants) {
                    $eventsParticipant = new EventsParticipants();
                    $eventsParticipant->setKey(sha1(time()))
                                       ->setEventsId($data['event_id']);

                }

                $eventsParticipant->setInvitedBy($customer->getId())
                    ->setEmail($data['email'])
                    ->setPhone($data['phone'])
                    ->setFirstName($data['first_name'])
                    ->setLastName($data['last_name'])
                    ->setTellAFriend($data['tell_a_friend'])
                    ->save();

                // Now send out some emails!
                if ($eventsParticipant->getEmail()) {
                    $mailer = $this->get('mail_manager');

                    $mailer->setMessage('events.participant.invited', [
                        'event_date'       => $event->getEventDate('d/m'),
                        'event_time'       => $event->getEventDate('H:i'),
                        'to_name'          => $eventsParticipant->getFirstName(). ' ' .$eventsParticipant->getLastName(),
                        'hostess'          => $event->getHost(),
                        'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                        'zip'              => $event->getPostalCode(),
                        'city'             => $event->getCity(),
                        'email'            => $event->getEmail(),
                        'phone'            => $event->getPhone(),
                        'link'             => $this->generateUrl('events_rsvp', ['key' => $eventsParticipant->getKey()], true),
                        'consultant_name'  => $myEvents[$data['event_id']]['data']->getCustomersRelatedByConsultantsId()->getFirstName(). ' ' .$myEvents[$data['event_id']]['data']->getCustomersRelatedByConsultantsId()->getLastName(),
                        'consultant_email' => $myEvents[$data['event_id']]['data']->getCustomersRelatedByConsultantsId()->getEmail()
                    ]);

                    $mailer->setTo(
                        $eventsParticipant->getEmail(),
                        $eventsParticipant->getFirstName(). ' ' .$eventsParticipant->getLastName()
                    );

                    $mailer->setFrom(['events@pompdelux.com' => $event->getHost(). ' (via POMPdeLUX)']);
                    $mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
                    $mailer->setReplyTo($event->getEmail(), $event->getHost());
                    $mailer->send();
                }

                if ($eventsParticipant->getPhone()) {
                    $this->get('sms_manager')->sendEventInvite($eventsParticipant);
                }

                $this->get('session')->getFlashBag()->add('notice', 'events.participant.invited');
            }
        }

        // Store all participants in the master events array.
        foreach ($events as $event) {
            if ($event instanceof Events) {
                $eventsParticipants = EventsParticipantsQuery::create()->findByEventsId($event->getId());
                $myEvents[$event->getId()]['participants'] = $eventsParticipants;
            }
        }

        return $this->render('EventsBundle:Events:myEvents.html.twig', [
            'page_type' => 'event',
            'events'    => $myEvents
        ]);
    }
}
