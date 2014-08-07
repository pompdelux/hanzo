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
use Hanzo\Model\EventsParticipantsQuery;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

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
     * @var EventHostess
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
     * @param Events       $event
     * @param EventHostess $hostess
     * @param Customers    $consultant
     */
    public function setEventData(Events $event, EventHostess $hostess, Customers $consultant)
    {
        $this->event      = $event;
        $this->hostess    = $hostess->getHostess();
        $this->consultant = $consultant;
    }

    public function sendHostessEmail()
    {
        if ($this->event->getNotifyHostess()){

            // Send an email to the new Host
            $this->mailer->setMessage('events.hostess.create', array(
                'event_date'       => $this->event->getEventDate('d/m'),
                'event_time'       => $this->event->getEventDate('H:i'),
                'to_name'          => $this->event->getHost(),
                'address'          => $this->event->getAddressLine1(). ' ' .$this->event->getAddressLine2(),
                'zip'              => $this->event->getPostalCode(),
                'city'             => $this->event->getCity(),
                'email'            => $this->hostess->getEmail(),
                'password'         => $this->hostess->getPasswordClear(),
                'phone'            => $this->event->getPhone(),
                'link'             => $this->generateUrl('events_invite', array('key' => $this->event->getKey()), true),
                'consultant_name'  => $this->consultant->getFirstName(). ' ' .$this->consultant->getLastName(),
                'consultant_email' => $this->consultant->getEmail()
            ));

            $this->mailer->setTo(array($this->event->getEmail() => $this->event->getHost()));
            $this->mailer->setFrom(array($this->consultant->getEmail() => $this->consultant->getFirstName(). ' ' .$this->consultant->getLastName()));
            $this->mailer->send();
        }
    }

    public function sendChangeHostessEmail(Events $oldEvent)
    {
        $consultant = CustomersPeer::getCurrent();

        if ($this->event->getNotifyHostess()) {
            // Send an email to the old Host
            $this->mailer->setMessage('events.hostess.eventmovedfrom', array(
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
                'link'             => $this->router->generate('events_invite', array('key' => $this->event->getKey()), true),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ));

            $this->mailer->setTo(array($oldEvent->getEmail() => $oldEvent->getHost()));
            $this->mailer->setFrom(array($consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()));
            $this->mailer->send();

            // Send an email to the new Host
            $this->mailer->setMessage('events.hostess.eventmovedto', array(
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
                'email'            => $this->hostess->getHostess()->getEmail(),
                'password'         => $this->hostess->getHostess()->getPasswordClear(),
                'phone'            => $this->event->getPhone(),
                'link'             => $this->router->generate('events_invite', array('key' => $this->event->getKey()), true),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ));

            $this->mailer->setTo(array($this->event->getEmail() => $this->event->getHost()));
            $this->mailer->setFrom(array($consultant->getEmail() => $consultant->getFirstName(). ' ' .$consultant->getLastName()));
            $this->mailer->send();
        }
    }

    public function sendParticipantEventChangesEmail()
    {
        $consultant = CustomersPeer::getCurrent();

        // Find all participants.
        $participants = EventsParticipantsQuery::create()
            ->filterByEventsId($this->event->getId())
            ->find()
        ;

        // Send an email to all participants
        foreach ($participants as $participant) {
            /** @var \Hanzo\Model\EventsParticipants $participant */
            if (!$participant->getEmail()) {
                continue;
            }

            $this->mailer->setMessage('events.participant.eventchanged', array(
                'event_date'       => $this->event->getEventDate('d/m'),
                'event_time'       => $this->event->getEventDate('H:i'),
                'to_name'          => $participant->getFirstName(). ' ' .$participant->getLastName(),
                'hostess'          => $this->event->getHost(),
                'address'          => $this->event->getAddressLine1(). ' ' .$this->event->getAddressLine2(),
                'zip'              => $this->event->getPostalCode(),
                'city'             => $this->event->getCity(),
                'phone'            => $this->event->getPhone(),
                'email'            => $this->event->getEmail(),
                'link'             => $this->router->generate('events_rsvp', array('key' => $participant->getKey()), true),
                'consultant_name'  => $consultant->getFirstName(). ' ' .$consultant->getLastName(),
                'consultant_email' => $consultant->getEmail()
            ));

            $this->mailer->setTo(array($participant->getEmail() => $participant->getFirstName(). ' ' .$participant->getLastName()));
            $this->mailer->setFrom(array($this->event->getEmail() => $this->event->getHost()));
            $this->mailer->send();
        }
    }
}
