<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\EventsQuery;
use Hanzo\Model\EventsParticipantsQuery;
use Hanzo\Model\MessagesI18nQuery;

use Smesg\Adapter\PhpStreamAdapter;
use Smesg\Provider\UnwireProvider;

class SmsService
{
    protected $parameters;
    protected $settings;

    public function __construct($parameters, $settings)
    {
        $this->parameters = $parameters;

        // unwire
        // $settings['provider.service'];
        // $settings['provider.user'];
        // $settings['provider.password'];
        // $settings['provider.appnr'];
        // $settings['provider.mediacode'];
        // $settings['provider.price'];

        $this->settings['provider.get_smsc'] = 0;
        $this->settings = $settings;
    }

    /**
     * send sms reminders to event participants.
     *
     * @return array responses from the sms gateway
     */
    public function eventReminder($locale = 'da_DK')
    {
        $provider = new UnwireProvider(new PhpStreamAdapter(), array(
            'user' => $this->settings['provider.user'],
            'password' => $this->settings['provider.password'],
            'appnr' => $this->settings['provider.appnr'],
            'mediacode' => $this->settings['provider.mediacode'],
            'price' => $this->settings['provider.price'],
            'get_smsc' => (boolean) $this->settings['provider.get_smsc'],
        ));

        $message = MessagesI18nQuery::create()
            ->joinWithMessages()
            ->filterByLocale($locale)
            ->useMessagesQuery()
                ->filterByNs('sms')
                ->filterByKey('event.reminder')
            ->endUse()
            ->findOne()
        ;

        if (!$message) {
            throw new Exception("No 'event.reminder' translation for '{$locale}'", 1);
        }

        $message = $message->getBody();

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
            ->filterByPhone(NULL, \Criteria::ISNOTNULL)
            ->filterBySmsSendAt(NULL, \Criteria::ISNULL)
            ->find()
        ;

        $batches = array();
        foreach ($participants as $participant) {
            $event = $participant->getEvents();

            $batches[(int) $participant->getPhone()] = strtr($message, array(
                ':first_name:' => $participant->getFirstName(),
                ':last_name:' => $participant->getLastName(),
                ':event_date:' => $event->getEventDate('d-m-Y'),
                ':event_time:' => $event->getEventDate('G:i'),
                ':address:' => $event->getAddressLine1(),
                ':postal_code:' => $event->getPostalCode(),
                ':city:' => $event->getCity(),
            ));

            // mark participant as notified
            $participant->setSmsSendAt('now');
            $participant->save();
        }

        $responses = array();
        foreach (array_chunk($batches, UnwireProvider::BATCH_MAX_QUANTITY) as $batch) {
            foreach ($batch as $mobile_number => $message) {
                $responses[] = $provider->addMessage($mobile_number, $message);
            }
            $provider->send();
        }

        return $responses;
    }
}
