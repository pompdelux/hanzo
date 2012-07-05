<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class SmsController extends CoreController
{
    public function rsvpAction()
    {
        Tools::log($_GET);

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
