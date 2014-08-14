<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\EventsBundle\Command;

use Hanzo\Model\EventsQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResendEventInvitesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:events:resend-invites')
            ->setDescription('Resend invitations for selected range of events.')
            ->addArgument('events', InputArgument::REQUIRED, "List of event id's seperated by ','")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $events = EventsQuery::create()
            ->filterById(explode(',', $input->getArgument('events')))
            ->find();

        $router = $this->getContainer()->get('router');
        $mailer = $this->getContainer()->get('mail_manager');

        /** @var \Hanzo\Model\Events $event */
        foreach ($events as $event) {
            /** @var \Hanzo\Model\EventsParticipants */
            foreach ($event->getEventsParticipantss() as $participant) {
                if ($participant->getEmail()) {
                    $sa = $event->getCustomersRelatedByConsultantsId();

                    $mailer->setMessage('events.participant.invited', array(
                        'event_date'       => $event->getEventDate('d/m'),
                        'event_time'       => $event->getEventDate('H:i'),
                        'to_name'          => $participant->getFirstName(). ' ' .$participant->getLastName(),
                        'hostess'          => $event->getHost(),
                        'address'          => $event->getAddressLine1(). ' ' .$event->getAddressLine2(),
                        'zip'              => $event->getPostalCode(),
                        'city'             => $event->getCity(),
                        'email'            => $event->getEmail(),
                        'phone'            => $event->getPhone(),
                        'link'             => $router->generate('events_rsvp', array('key' => $participant->getKey(), '_locale' => $this->getContainer()->getParameter('locale')), true),
                        'consultant_name'  => $sa->getName(),
                        'consultant_email' => $sa->getEmail(),
                    ));

                    $mailer
                        ->setTo($participant->getEmail(), $participant->getFirstName(). ' ' .$participant->getLastName())
                        ->setFrom(array('events@pompdelux.com' => $event->getHost(). ' (via POMPdeLUX)'))
                        ->setSender('events@pompdelux.com', 'POMPdeLUX', true)
                        ->setReplyTo($event->getEmail(), $event->getHost())
                        ->send();
                }

                if ($participant->getPhone()) {
                    try {
                        $this->getContainer()
                            ->get('sms_manager')
                            ->sendEventInvite($participant);
                    } catch (\Exception $e) {}
                }
            }
        }
    }
}
