<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

use Hanzo\Bundle\NewsletterBundle\Providers\MailPlatformRequest,
    Guzzle\Http\Client;
    ;

class MailPlatformProvider extends BaseProvider
{
    public function subscriberCreate($subscriber_id, $params)
    {
    }
    public function subscriberUpdate($subscriber_id, $params)
    {
    }
    public function subscriberDelete($subscriber_id)
    {
    }

    public function subscriberGet($subscriber_id)
    {
        // FIXME:
        $username = 'pompdelux_dk';
        $token    = 'c4bfaa0026f352e13aab064ea623e6cec3703e64';
        $url      = 'http://client2.mailmailmail.net/';
        // $url ='http://requestb.in/';
        $client   = new Client($url);
        $request = new MailPlatformRequest($username, $token, $url, $client);
        //$request = $this->container->get('MailPlatformRequest');

        $request->type = 'subscribers';
        $request->method = 'GetSubscribers';
        $request->body =  [
            'details' => [
                'searchinfo' => [ 'Email' => [ 'data' => $subscriber_id, 'exactly' => true ] ]
                ],
        ];
        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function subscriberActivate($subscriber_id)
    {
    }

    public function subscriberIsSubscribed($subscriber_id, $list_ids)
    {
    }
    public function subscriberAddToList($subscriber_id, $list_id)
    {
    }
    public function subscriberGetSubscribedLists($subscriber_id)
    {
    }

    public function listsGet($params)
    {
    }

}
