<?php
/*
unwire addresser:
  194.192.81.90
  194.192.81.91
  194.192.81.100
  194.192.81.101
*/

namespace Hanzo\Bundle\EventsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;

/**
 * Class SmsController
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class SmsController extends CoreController
{
    // TODO this should not be hardcoded ! but we need to figure out where to store the information...
    protected $appnrMap = [
        1231          => 45,  // dk
        2201          => 47,  // no
        17163         => 358, // fi
        72445         => 46,  // se
        31625585489   => 31,  // nl
        4915142359909 => 49,  // de
    ];

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rsvpAction(Request $request)
    {
        // _GET: Array (
        //     [sender] => 4529927366
        //     [smsc] => tdc
        //     [appnr] => 1231
        //     [text] => pdl e1234
        //     [sessionid] => 4529927366:20120730221204
        // )

        $sender = trim($request->query->get('sender'));
        $appnr  = trim($request->query->get('appnr'));
        $text   = trim($request->query->get('text'));

        if (in_array($appnr, array_keys($this->appnrMap)) &&
            (strtolower(substr($text, 0, 5)) == 'pdl e')
        ) {
            // we expect the message to be "mediacode event_id[ optional junk]"
            @list($mediacode, $eventId, $junk) = explode(' ', $text, 3);

            $eventId = preg_replace('/[^0-9]+/', '', $eventId);

            // we need to strip the country code from the phone number.
            $lookup = substr($sender, strlen($this->appnrMap[$appnr]));

            $participant = EventsParticipantsQuery::create()
                ->joinWithEvents()
                ->filterByEventsId($eventId)
                ->filterByPhone($lookup)
                ->_or()
                ->filterByPhone('0'.$lookup)
                ->_or()
                ->filterByPhone($sender)
                ->filterByRespondedAt(null, \Criteria::ISNULL)
                ->findOne();

            if ($participant instanceof EventsParticipants) {
                try {
                    $participant->setHasAccepted(true);
                    $participant->setRespondedAt(time());
                    $participant->save();

                    $this->get('sms_manager')->sendEventConfirmationReply($participant);
                } catch (\Exception $e) {
                }
            }
        }

        // no response needed
        return $this->response('');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendtestAction()
    {
        $p = \Hanzo\Model\EventsParticipantsQuery::create()->findOneById(387);
        $p = $this->container->get('sms_manager')->sendEventInvite($p);
        return $this->response('<pre>'.print_r($p, 1).'</pre>');

        $p = Hanzo::getInstance()->getByNs('sms');
        return $this->response('<pre>'.print_r($p, 1).'</pre>');


        // http://..../events/sms/test-123
        // dansk.: 29927366 / 1231
        // svensk: 0739415117 / 72445

        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-type: application/x-www-form-urlencoded",
                'max_redirects' => 0,
                'timeout'       => 5,
                'content'       => http_build_query([
                    // 'smsc' => 'tdc.telmore',
                    'user'      => 'pompdelux',
                    'password'  => 'fpgyhiu345',
                    'to'        => 46 . ltrim('0739415117', '0'),
                    'price'     => '0.00SEK',
                    'appnr'     => 72445,
                    'text'      => 'Hej Jeanette, lige en test fra det svenske konsulentsite. Vil du ikke være så venlig at svare tilbage på denne sms ? Bare skriv "hej" eller noget :) Mvh Heinrich',
                    'mediacode' => 'pdl',
                    'sessionid' => '4529927366:' . date('Ymdhis'),
                ])
        ]]);

        $response        = file_get_contents('https://gw.unwire.com/service/smspush', false, $context);
        $responseHeaders = $http_response_header;

        $out = "<pre>".print_r($responseHeaders, 1)."\n".$response."</pre>";

        return $this->response($out);
    }
}
