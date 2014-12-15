<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\EventsBundle\Helpers;

use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Events;
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\EventsParticipantsQuery;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class EventMailer
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class EventMailer
{
    /**
     * @var MailService
     */
    private $mailer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Events
     */
    private $event;

    /**
     * @var Customers
     */
    private $hostess;

    /**
     * @var Customers
     */
    private $consultant;

    /**
     * @param MailService $mailer
     * @param Router      $router
     */
    public function __construct(MailService $mailer, Router $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    /**
     * @param Events                      $event
     * @param EventHostess|Customers|null $hostess
     * @param Customers                   $consultant
     */
    public function setEventData(Events $event, $hostess = null, Customers $consultant = null)
    {
        $this->event      = $event;
        $this->consultant = $consultant;

        if ($hostess instanceof EventHostess) {
            $this->hostess = $hostess->getHostess();
        } elseif ($hostess instanceof Customers) {
            $this->hostess = $hostess;
        }
    }

    /**
     * Send event created email to hostess.
     */
    public function sendHostessEmail()
    {
        if ($this->event->getNotifyHostess()) {
            // Send an email to the new Host
            $this->mailer->setMessage('events.hostess.create', [
                'event_date'       => $this->event->getEventDate('d/m'),
                'event_time'       => $this->event->getEventDate('H:i'),
                'name'          => $this->event->getHost(),
                'to_name'          => $this->event->getHost(),
                'address'          => $this->event->getAddressLine1(). ' ' .$this->event->getAddressLine2(),
                'zip'              => $this->event->getPostalCode(),
                'city'             => $this->event->getCity(),
                'email'            => $this->hostess->getEmail(),
                'password'         => $this->hostess->getPasswordClear(),
                'phone'            => $this->event->getPhone(),
                'link'             => $this->router->generate('events_invite', ['key' => $this->event->getKey()], true),
                'consultant_name'  => $this->consultant->getFirstName(). ' ' .$this->consultant->getLastName(),
                'consultant_email' => $this->consultant->getEmail()
            ]);

            $this->mailer->setTo([$this->event->getEmail() => $this->event->getHost()]);
            $this->mailer->setFrom([$this->consultant->getEmail() => $this->consultant->getFirstName(). ' ' .$this->consultant->getLastName()]);
            $this->mailer->send();
        }
    }

    /**
     * Send event moved emails to hostess.
     *
     * @param Events $oldEvent
     *
     * @throws \PropelException
     */
    public function sendChangeHostessEmail(Events $oldEvent)
    {
        $consultant = CustomersPeer::getCurrent();

        if ($this->event->getNotifyHostess()) {
            // Send an email to the old Host
            $this->mailer->setMessage('events.hostess.eventmovedfrom', [
                'event_date'       => $this->event->getEventDate('d/m'),
                'event_time'       => $this->event->getEventDate('H:i'),
                'name'             => $oldEvent->getHost(),
                'from_address'     => $oldEvent->getAddressLine1(). ' ' .$oldEvent->getAddressLine2(),
                'from_zip'         => $oldEvent->getPostalCode(),
                'from_city'        => $oldEvent->getCity(),
                'to_name'          => $this->event->getHost(),
                'to_address'       => $this->event->getAddressLine1(). ' ' .$this->event->getAddressLine2(),
                'to_zip'           => $this->event->getPostalCode(),
                'to_city'          => $this->event->getCity(),
                'to_phone'         => $this->event->getPhone(),
                'link'             => $this->router->generate('events_invite', ['key' => $this->event->getKey()], true),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ]);

            $this->mailer->setTo([$oldEvent->getEmail() => $oldEvent->getHost()]);
            $this->mailer->setFrom([$consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()]);
            $this->mailer->send();

            // Send an email to the new Host
            $this->mailer->setMessage('events.hostess.eventmovedto', [
                'event_date'       => $this->event->getEventDate('d/m'),
                'event_time'       => $this->event->getEventDate('H:i'),
                'from_name'        => $oldEvent->getHost(),
                'from_address'     => $oldEvent->getAddressLine1(). ' ' .$oldEvent->getAddressLine2(),
                'from_zip'         => $oldEvent->getPostalCode(),
                'from_city'        => $oldEvent->getCity(),
                'from_phone'       => $oldEvent->getPhone(),
                'name'             => $this->event->getHost(),
                'to_address'       => $this->event->getAddressLine1(). ' ' .$this->event->getAddressLine2(),
                'to_zip'           => $this->event->getPostalCode(),
                'to_city'          => $this->event->getCity(),
                'email'            => $this->hostess->getEmail(),
                'password'         => $this->hostess->getPasswordClear(),
                'phone'            => $this->event->getPhone(),
                'link'             => $this->router->generate('events_invite', ['key' => $this->event->getKey()], true),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ]);

            $this->mailer->setTo([$this->event->getEmail() => $this->event->getHost()]);
            $this->mailer->setFrom([$consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()]);
            $this->mailer->send();
        }
    }

    /**
     * Send event changed emails to participants.
     */
    public function sendParticipantEventChangesEmail()
    {
        $consultant = CustomersPeer::getCurrent();

        // Find all participants.
        $participants = EventsParticipantsQuery::create()
            ->filterByEventsId($this->event->getId())
            ->find();

        // Send an email to all participants
        foreach ($participants as $participant) {
            /** @var \Hanzo\Model\EventsParticipants $participant */
            if (!$participant->getEmail()) {
                continue;
            }

            $this->mailer->setMessage('events.participant.eventchanged', [
                'event_date'       => $this->event->getEventDate('d/m'),
                'event_time'       => $this->event->getEventDate('H:i'),
                'to_name'          => $participant->getFirstName(). ' ' .$participant->getLastName(),
                'hostess'          => $this->event->getHost(),
                'address'          => $this->event->getAddressLine1(). ' ' .$this->event->getAddressLine2(),
                'zip'              => $this->event->getPostalCode(),
                'city'             => $this->event->getCity(),
                'phone'            => $this->event->getPhone(),
                'email'            => $this->event->getEmail(),
                'link'             => $this->router->generate('events_rsvp', ['key' => $participant->getKey()], true),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ]);

            $this->mailer->setTo([$participant->getEmail() => $participant->getFirstName(). ' ' .$participant->getLastName()]);
            // note, we use "via" because amazon does not allow us to set arbitrary from addresses.
            $this->mailer->setFrom(['events@pompdelux.com' => $this->event->getHost() . ' (via POMPdeLUX)']);
            $this->mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
            $this->mailer->setReplyTo($this->event->getEmail(), $this->event->getHost());
            $this->mailer->send();
        }
    }

    /**
     * Send event participant invite mail.
     *
     * @param EventsParticipants $eventsParticipant
     */
    public function sendParticipantEventInviteEmail(EventsParticipants $eventsParticipant)
    {
        $this->mailer->setMessage('events.participant.invited', [
            'event_date'       => $this->event->getEventDate('d/m'),
            'event_time'       => $this->event->getEventDate('H:i'),
            'name'             => $eventsParticipant->getFirstName() . ' ' . $eventsParticipant->getLastName(),
            'to_name'          => $eventsParticipant->getFirstName() . ' ' . $eventsParticipant->getLastName(),
            'hostess'          => $this->event->getHost(),
            'address'          => $this->event->getAddressLine1() . ' ' . $this->event->getAddressLine2(),
            'zip'              => $this->event->getPostalCode(),
            'city'             => $this->event->getCity(),
            'email'            => $this->event->getEmail(),
            'phone'            => $this->event->getPhone(),
            'link'             => $this->router->generate('events_rsvp', ['key' => $eventsParticipant->getKey()], true),
            'consultant_name'  => $this->consultant->getFirstName() . ' ' . $this->consultant->getLastName(),
            'consultant_email' => $this->consultant->getEmail()
        ]);

        $this->mailer->setTo(
            $eventsParticipant->getEmail(),
            $eventsParticipant->getFirstName(). ' ' .$eventsParticipant->getLastName()
        );

        $this->mailer->setFrom(['events@pompdelux.com' => $this->event->getHost() . ' (via POMPdeLUX)']);
        $this->mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
        $this->mailer->setReplyTo($this->event->getEmail(), $this->event->getHost());
        $this->mailer->send();
    }

    /**
     * Send email to event hostess if an event is canceled.
     */
    public function sendHostessDeletedEventEmail()
    {
        $this->mailer->setMessage('events.hostess.delete', [
            'event_date'       => $this->event->getEventDate('d/m'),
            'event_time'       => $this->event->getEventDate('H:i'),
            'to_name'          => $this->event->getHost(),
            'address'          => $this->event->getAddressLine1(). ' ' .$this->event->getAddressLine2(),
            'zip'              => $this->event->getPostalCode(),
            'city'             => $this->event->getCity(),
            'consultant_name'  => $this->consultant->getFirstName(). ' ' .$this->consultant->getLastName(),
            'consultant_email' => $this->consultant->getEmail()
        ]);

        $this->mailer->setTo([$this->event->getEmail() => $this->event->getHost()]);
        $this->mailer->setFrom([$this->consultant->getEmail() => $this->consultant->getFirstName(). ' ' .$this->consultant->getLastName()]);
        $this->mailer->send();
    }

    /**
     * Send email to event participants if an event is canceled.
     *
     * @param EventsParticipants $participant
     */
    public function sendParticipantDeletedEventEmail(EventsParticipants $participant)
    {
        $this->mailer->setMessage('events.participants.delete', [
            'event_date'    => $this->event->getEventDate('d/m'),
            'event_time'    => $this->event->getEventDate('H:i'),
            'to_name'       => $participant->getFirstName(),
            'address'       => $this->event->getAddressLine1().' ' .$this->event->getAddressLine2(),
            'zip'           => $this->event->getPostalCode(),
            'city'          => $this->event->getCity(),
            'hostess'       => $this->event->getHost(),
            'hostess_email' => $this->event->getEmail()
        ]);

        $this->mailer->setTo($participant->getEmail(), $participant->getFirstName(). ' ' .$participant->getLastName());
        // note, we use "via" because amazon does not allow us to set arbitrary from addresses.
        $this->mailer->setFrom(['events@pompdelux.com' => $this->event->getHost() . ' (via POMPdeLUX)']);
        $this->mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
        $this->mailer->setReplyTo($this->event->getEmail(), $this->event->getHost());
        $this->mailer->send();
    }

    /**
     * @param \DateTime $oldDate
     *
     * @throws \PropelException
     */
    public function sendDateChangedHostessEmail(\DateTime $oldDate)
    {
        $this->mailer->setMessage('events.hostess.event_date_changed', [
            'to_event_date'    => $this->event->getEventDate('d/m'),
            'to_event_time'    => $this->event->getEventDate('H:i'),
            'from_event_date'  => $oldDate->format('d/m'),
            'from_event_time'  => $oldDate->format('H:i'),
            'name'             => $this->event->getHost(),
            'address'          => $this->event->getAddressLine1() . ' ' . $this->event->getAddressLine2(),
            'zip'              => $this->event->getPostalCode(),
            'city'             => $this->event->getCity(),
            'email'            => $this->hostess->getEmail(),
            'password'         => $this->hostess->getPasswordClear(),
            'phone'            => $this->event->getPhone(),
            'link'             => $this->router->generate('events_invite', ['key' => $this->event->getKey()], true),
            'consultant_name'  => $this->consultant->getFirstName() . ' ' . $this->consultant->getLastName(),
            'consultant_email' => $this->consultant->getEmail()
        ]);

        $this->mailer->setTo([$this->event->getEmail() => $this->event->getHost()]);
        $this->mailer->setFrom([$this->consultant->getEmail() => $this->consultant->getFirstName(). ' ' .$this->consultant->getLastName()]);
        $this->mailer->send();
    }

    /**
     * @param \DateTime $oldDate
     *
     * @throws \PropelException
     */
    public function sendDateChangedParticipantEmail(\DateTime $oldDate)
    {
        // Find all participants.
        $participants = EventsParticipantsQuery::create()
            ->filterByEventsId($this->event->getId())
            ->find();

        // Send an email to all participants
        foreach ($participants as $participant) {
            /** @var \Hanzo\Model\EventsParticipants $participant */
            if (!$participant->getEmail()) {
                continue;
            }

            $this->mailer->setMessage('events.participant.event_date_changed', [
                'to_event_date'    => $this->event->getEventDate('d/m'),
                'to_event_time'    => $this->event->getEventDate('H:i'),
                'from_event_date'  => $oldDate->format('d/m'),
                'from_event_time'  => $oldDate->format('H:i'),
                'name'             => $participant->getFirstName() . ' ' . $participant->getLastName(),
                'hostess'          => $this->event->getHost(),
                'address'          => $this->event->getAddressLine1() . ' ' . $this->event->getAddressLine2(),
                'zip'              => $this->event->getPostalCode(),
                'city'             => $this->event->getCity(),
                'phone'            => $this->event->getPhone(),
                'email'            => $this->event->getEmail(),
                'link'             => $this->router->generate('events_rsvp', ['key' => $participant->getKey()], true),
                'consultant_name'  => $this->consultant->getFirstName() . ' ' . $this->consultant->getLastName(),
                'consultant_email' => $this->consultant->getEmail()
            ]);

            $this->mailer->setTo([$participant->getEmail() => $participant->getFirstName(). ' ' .$participant->getLastName()]);
            // note, we use "via" because amazon does not allow us to set arbitrary from addresses.
            $this->mailer->setFrom(['events@pompdelux.com' => $this->event->getHost() . ' (via POMPdeLUX)']);
            $this->mailer->setSender('events@pompdelux.com', 'POMPdeLUX', true);
            $this->mailer->setReplyTo($this->event->getEmail(), $this->event->getHost());
            $this->mailer->send();
        }
    }
}
