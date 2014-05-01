<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Criteria;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\EventsQuery;
use Hanzo\Model\EventsParticipantsQuery;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\MessagesI18nQuery;

use Smesg\Adapter\PhpStreamAdapter;
use Smesg\Provider\UnwireProvider;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class SmsService
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param $parameters
     * @param $settings
     * @throws \InvalidArgumentException
     */
    public function __construct($parameters, $settings)
    {
        if (!$parameters[0] instanceof Translator) {
            throw new \InvalidArgumentException('Translator expected as first parameter.');
        }

        $this->translator = $parameters[0];

        // unwire
        // $settings['provider.service'];
        // $settings['provider.user'];
        // $settings['provider.password'];
        // $settings['provider.appnr'];
        // $settings['provider.mediacode'];
        // $settings['provider.price'];
        // $settings['provider.get_smsc'];

        // defaults
        $this->settings['provider.get_smsc']        = 0;
        $this->settings['send.event.reminders']     = 0;
        $this->settings['send.event.confirmations'] = 0;
        $this->settings['send.event.confirmations'] = 0;

        $this->settings = $settings;
    }


    /**
     * check whether or not reminders is enabled for this account.
     *
     * @return bool
     */
    public function isEventRemindersEnabled()
    {
        return (bool) $this->settings['send.event.reminders'];
    }


    /**
     * Send invite via sms
     *
     * @param $participant
     * @return bool|mixed|\Smesg\Provider\Common\Response
     */
    public function sendEventInvite($participant)
    {
        if ((false === Tools::isBellcomRequest()) && (0 == $this->settings['send.event.invites'])) {
            return;
        }

        $event = $participant->getEvents();
        $parameters = array(
            '%name%' => trim($participant->getFirstName().' '.$participant->getLastName()),
            '%event_date%' => $event->getEventDate('d-m-Y'),
            '%event_time%' => $event->getEventDate('G:i'),
            '%address%' => $event->getAddressLine1(),
            '%zip%' => $event->getPostalCode(),
            '%city%' => $event->getCity(),
            '%hostess%' => $event->getHost(),
            '%event_id%' => 'e'.$event->getId(),
        );

        $to = $this->settings['provider.calling_code'].ltrim($participant->getPhone(), '0');
        $message = $this->translator->trans('event.sms.invite', $parameters, 'events');

        $provider = $this->getProvider();
        $provider->addMessage($to, utf8_decode($message));

        $response = $provider->send();

        return $response;
    }


    /**
     * Send confirmation sms
     *
     * @param $participant
     * @return bool|mixed|\Smesg\Provider\Common\Response
     */
    public function sendEventConfirmationReply($participant)
    {
        $event = $participant->getEvents();
        $parameters = array(
            '%name%' => $participant->getFirstName(),
            '%event_date%' => $event->getEventDate('d-m-Y'),
            '%event_time%' => $event->getEventDate('G:i'),
            '%address%' => $event->getAddressLine1(),
            '%zip%' => $event->getPostalCode(),
            '%city%' => $event->getCity(),
            '%hostess%' => $event->getHost(),
        );

        $to = $this->settings['provider.calling_code'].ltrim($participant->getPhone(), '0');
        $message = $this->translator->trans('event.sms.confirmation.reply', $parameters, 'events');

        $provider = $this->getProvider();
        $provider->addMessage($to, utf8_decode($message));

        $response = $provider->send();

        return $response;
    }


    /**
     * send sms reminders to event participants.
     *
     * @param string $locale
     * @return array
     */
    public function eventReminder($locale = 'da_DK')
    {
        if ((0 == $this->settings['send.event.reminders'])) {
            return;
        }

        $provider = $this->getProvider();

        $date = new \DateTime();
        $date->modify('+1 day midnight');
        $min = $date->format('Y-m-d H:i:s');
        $date->modify('+1 day');
        $max = $date->format('Y-m-d H:i:s');

        $participants = EventsParticipantsQuery::create()
            ->joinWithEvents()
            ->useEventsQuery()
                ->filterByEventDate(array(
                    'min' => $min,
                    'max' => $max
                ))
            ->endUse()
            ->filterByNotifyBySms(true)
            ->filterByPhone(NULL, Criteria::ISNOTNULL)
            ->filterBySmsSendAt(NULL, Criteria::ISNULL)
            ->find()
        ;

        $batches = array();
        foreach ($participants as $participant) {
            $event = $participant->getEvents();
            $to = $this->settings['provider.calling_code'].ltrim($participant->getPhone(), '0');

            $parameters = array(
                '%name%' => $participant->getFirstName(),
                '%event_date%' => $event->getEventDate('d-m-Y'),
                '%event_time%' => $event->getEventDate('G:i'),
                '%address%' => $event->getAddressLine1(),
                '%zip%' => $event->getPostalCode(),
                '%city%' => $event->getCity(),
                '%hostess%' => $event->getHost(),
            );

            $batches[$to] = $message = $this->translator->trans('event.sms.reminder', $parameters, 'events');

            // mark participant as notified
            $participant->setSmsSendAt('now');
            $participant->save();
        }

        $responses = array();
        foreach (array_chunk($batches, UnwireProvider::BATCH_MAX_QUANTITY) as $batch) {
            foreach ($batch as $to => $message) {
                $responses[] = $provider->addMessage($to, $message);
            }
            $provider->send();
        }

        return $responses;
    }


    /**
     * @return UnwireProvider
     */
    protected function getProvider()
    {
        return new UnwireProvider(new PhpStreamAdapter(), array(
            'user'      => $this->settings['provider.user'],
            'password'  => $this->settings['provider.password'],
            'appnr'     => $this->settings['provider.appnr'],
            'mediacode' => $this->settings['provider.mediacode'],
            'price'     => $this->settings['provider.price'],
            'get_smsc'  => (boolean) $this->settings['provider.get_smsc'],
        ));
    }
}
