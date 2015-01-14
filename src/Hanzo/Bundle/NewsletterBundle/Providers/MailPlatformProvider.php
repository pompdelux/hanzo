<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

use Hanzo\Bundle\NewsletterBundle\Providers\MailPlatformRequest
    ;

class MailPlatformProvider extends BaseProvider
{
    public function subscriberCreate($subscriber_id, $list_id, Array $params = [])
    {
        return $this->subscriberAddToList($subscriber_id, $list_id, $params);
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
        return $request->execute();
    }

    public function subscriberDelete($subscriber_id, $list_id = false)
    {
        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'delete';

        if ($list_id === false)
        {
            $list_id = '';
        }

        $requestBody = [
            'details' => [
                'emailaddress' => $subscriber_id,
                'listid'       => $list_id,
                ],
        ];

        $request->body = $requestBody;

        return $request->execute();
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

        return $request->execute();
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

        return $request->execute();
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

        return $request->execute();
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

        return $request->execute();
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

        return $request->execute();
    }

    public function listsGet(Array $params = [])
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

        $request->body = $requestBody;

        return $request->execute();
    }

    protected function getRequest()
    {
        // FIXME:
        $username = 'pompdelux_dk';
        $token    = 'c4bfaa0026f352e13aab064ea623e6cec3703e64';
        $baseUrl = 'http://client2.mailmailmail.net';
        $query   = 'xml.php';

        /*
         * $baseUrl = 'http://requestb.in';
         * $query   = 'pagkqjpa';
         */

        $client   = new \Guzzle\Http\Client($baseUrl);
        $request  = new MailPlatformRequest($username, $token, $query, $client);

        //$request = $this->container->get('MailPlatformRequest');

        return $request;
    }

    protected function getOptionalParams($optionalParams, $params, $requestBody)
    {
        foreach ($optionalParams as $fieldName)
        {
            if (isset($params[$fieldName]))
            {
                $requestBody['details'][$fieldName] = $params[$fieldName];
            }
        }

        return $requestBody;
    }
}
