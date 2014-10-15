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
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\EventsParticipantsQuery;

class SmsController extends CoreController
{

    // TODO this should not be hardcoded ! but we need to figure out where to store the information...
    protected $appnr_map = array(
        1231  => 45,  // dk
        2201  => 47,  // no
        17163 => 358, // fi
        72445 => 46,  // se
    );

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

        // TODO should not be bardcoded
        if (in_array($appnr, array(1231, 2201, 17163, 72445)) &&
            (strtolower(substr($text, 0, 5)) == 'pdl e')
        ) {
            @list($mediacode, $event_id, $junk) = explode(' ', $text, 3);
            $event_id = preg_replace('/[^0-9]+/', '', $event_id);

            // we need to strip the country code from the phone number.
            $lookup = substr($sender, strlen($this->appnr_map[$appnr]));

            $participant = EventsParticipantsQuery::create()
                ->joinWithEvents()
                ->filterByEventsId($event_id)
                ->filterByPhone($lookup)
                ->_or()
                ->filterByPhone('0'.$lookup)
                ->_or()
                ->filterByPhone($sender)
                ->filterByRespondedAt(null, \Criteria::ISNULL)
                ->findOne()
            ;

            if ($participant instanceof EventsParticipants) {
                try {
                    $participant->setHasAccepted(true);
                    $participant->setRespondedAt(time());
                    $participant->save();

                    $this->get('sms_manager')->sendEventConfirmationReply($participant);
                } catch (\Exception $e) {}
            }
        }

        // no response needed
        return $this->response('');
    }

    public function sendtestAction()
    {
        // http://..../events/sms/test-123
        // dansk.: 29927366 / 1231
        // svensk: 0739415117 / 72445

        $context = stream_context_create(array(
            'http' => array(
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded",
            'max_redirects' => 0,
            'timeout' => 5,
            'content' => http_build_query(array(
                'user' => 'pompdelux',
                'password' => 'fpgyhiu345',
                'to' => 46 . ltrim('0739415117', '0'),
                // 'smsc' => 'tdc.telmore',
                'price' => '0.00SEK',
                'appnr' => 72445,
                'text' => 'Hej Jeanette, lige en test fra det svenske konsulentsite. Vil du ikke være så venlig at svare tilbage på denne sms ? Bare skriv "hej" eller noget :) Mvh Heinrich',
                'mediacode' => 'pdl',
                'sessionid' => '4529927366:' . date('Ymdhis'),
            ))
        )));

        $response = file_get_contents('https://gw.unwire.com/service/smspush', false, $context);
        $response_headers = $http_response_header;

        $out = "<pre>".print_r($response_headers, 1)."\n".$response."</pre>";

        return $this->response($out);
    }
}
