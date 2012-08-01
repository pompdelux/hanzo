<?php
/*
unwire addresser:
  194.192.81.90
  194.192.81.91
  194.192.81.100
  194.192.81.101
*/

namespace Hanzo\Bundle\EventsBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\EventsParticipantsQuery;

class SmsController extends CoreController
{

    // TODO this should not be hardcoded ! but we need to figure out where to store the information...
    protected $appnr_map = array(
        1231 => 45,
        2201 => 47,
        17163 => 358,
        72445 => 46,
    );

    public function rsvpAction()
    {
        // Array (
        //     [sender] => 4529927366
        //     [smsc] => tdc
        //     [appnr] => 1231
        //     [text] => pdl e1234
        //     [sessionid] => 4529927366:20120730221204
        // )

        $sender = trim($this->getRequest()->get('sender'));
        $appnr = trim($this->getRequest()->get('appnr'));
        $smsc = trim($this->getRequest()->get('smsc'));
        $text = trim($this->getRequest()->get('text'));
        $sessionid = trim($this->getRequest()->get('sessionid'));

        // TODO should not be bardcoded
        if (in_array($appnr, array(1231, 2201, 17163, 72445)) &&
            (substr($text, 0, 5) == 'pdl e')
        ) {
            @list($mediacode, $event_id, $junk) = explode(' ', $text, 3);
            $event_id = trim($event_id, 'e');

            // we need to strip the country code from the phone number.
            $lookup = substr($sender, strlen($this->appnr_map[$appnr]));

            $participant = EventsParticipantsQuery::create()
                ->joinWithEvents()
                ->filterByEventsId($event_id)
                ->filterByPhone($lookup)
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
        $context = stream_context_create(array(
            'http' => array(
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded",
            'max_redirects' => 0,
            'timeout' => 5,
            'content' => http_build_query(array(
                'user' => 'pompdelux',
                'password' => 'fpgyhiu345',
                'to' => 4529927366,
                'smsc' => 'tdc.telmore',
                'price' => '0.00DKK',
                'appnr' => 1231,
                'text' => 'davs du !, svar med "pdl e1234"',
                'mediacode' => 'pdl',
                'sessionid' => '4529927366:' . date('Ymdhis'),
            ))
        )));

        echo file_get_contents('https://gw.unwire.com/service/smspush', FALSE, $context);
    }
}
