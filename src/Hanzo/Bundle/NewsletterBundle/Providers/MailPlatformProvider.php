<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

use Hanzo\Bundle\NewsletterBundle\Providers\MailPlatformRequest,
    Guzzle\Http\Client;
    ;

class MailPlatformProvider extends BaseProvider
{
    public function subscriberCreate($subscriber_id, $list_id, Array $params = [])
    {
        $this->subscriberAddToList($subscriber_id, $list_id, $params);
    }

    public function subscriberUpdate($subscriber_id, $list_id, Array $params = [])
    {
        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'update';

        $requestBody = [
            'details' => [
                'emailaddress' => $subscriber_id,
                'listid'       => $list_id,
                ],
        ];

        $optionalParams = ['customfields'];

        $requestBody = $this->getOptionalParams($optionalParams, $params, $requestBody);

        $request->body = $requestBody;
        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function subscriberDelete($subscriber_id, $list_id)
    {
        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'delete';
        $request->body   = [
            'details' => [
                'emailaddress' => $subscriber_id,
                'listid'       => $list_id,
                ],
        ];

        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function subscriberGet($subscriber_id)
    {
        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'GetSubscriberDetails';
        $request->body   = [
            'details' => [
                'emailaddress' => $subscriber_id,
                ],
        ];
        /*
         * $request->method = 'GetSubscribers';
         * $request->body   = [
         *     'details' => [
         *         'searchinfo' => [ 'Email' => [ 'data' => $subscriber_id, 'exactly' => true ] ]
         *         ],
         * ];
         */

        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function subscriberActivate($subscriber_id, $list_id)
    {
        $requestBody = [
            'details' => [
                'emailaddress' => $subscriber_id,
                'listid'      => $list_id,
                ],
        ];

        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'listid';
        $request->body   = $requestBody;

        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function subscriberIsSubscribed($subscriber_id, Array $list_ids)
    {
        $list_ids = implode(',', $list_ids);

        $requestBody = [
            'details' => [
                'emailaddress' => $subscriber_id,
                'listids'      => $list_ids,
                ],
        ];

        // $optionalParams = ['active_only', 'not_bounced', 'return_listid'];

        // $requestBody = $this->getOptionalParams($optionalParams, $params, $requestBody);

        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'IsSubscriberOnList';
        $request->body   = $requestBody;

        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function subscriberAddToList($subscriber_id, $list_id, $params = [])
    {
        $requestBody = [
            'details' => [
                'emailaddress' => $subscriber_id,
                'mailinglist'  => $list_id,
                'format'       => 'html',
                'confirmed'    => 1,
                ],
        ];

        $optionalParams = ['format', 'confirmed', 'add_to_autoresponders', 'customfields'];

        $requestBody = $this->getOptionalParams($optionalParams, $params, $requestBody);

        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'AddSubscriberToList';
        $request->body   = $requestBody;

        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function subscriberGetSubscribedLists($subscriber_id)
    {
        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'GetSubscriberDetails';
        $request->body   = [
            'details' => [
                'emailaddress' => $subscriber_id,
                ],
        ];

        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    public function listsGet($params)
    {
        $request         = $this->getRequest();
        $request->type   = 'lists';
        $request->method = 'GetLists';
        $requestBody   = [
            'details' => [
                'start'   => 0,
                'perpage' => 50,
                ],
        ];

        $optionalParams = ['start', 'perpage'];

        $requestBody = $this->getOptionalParams($optionalParams, $params, $requestBody);

        $request->body   = $requestBody;

        $response = $request->execute();
        error_log(__LINE__.':'.__FILE__.' '.print_r($response->xml(), 1)); // hf@bellcom.dk debugging
    }

    protected function getRequest()
    {
        // FIXME:
        $username = 'pompdelux_dk';
        $token    = 'c4bfaa0026f352e13aab064ea623e6cec3703e64';
        $url      = 'http://client2.mailmailmail.net/';
        // $url ='http://requestb.in/';
        $client   = new Client($url);
        $request = new MailPlatformRequest($username, $token, $url, $client);
        //$request = $this->container->get('MailPlatformRequest');

        return $request;
    }

    protected function getOptionalParams($optionalParams, $params, $requestBody)
    {
        foreach($optionalParams as $index => $value)
        {
            if (isset($params[$index]))
            {
                $requestBody['details'][$index] = $value;
            }
        }

        return $requestBody;
    }
}
