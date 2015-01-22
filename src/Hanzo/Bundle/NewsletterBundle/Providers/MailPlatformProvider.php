<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

use Hanzo\Core\Hanzo;

class MailPlatformProvider extends BaseProvider
{
    /**
     * subscriberCreate
     *
     * @see subscriberAddToList
     *
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function subscriberCreate($subscriber_id, $list_id, Array $params = [])
    {
        return $this->subscriberAddToList($subscriber_id, $list_id, $params);
    }

    /**
     * subscriberUpdate
     * - Update subscriber
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=Update
     *
     * @param string $subscriber_id
     * @param int $list_id
     * @param Array $params
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
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

    /**
     * subscriberDelete
     * - Remove a subscriber from a list, or all if list_id = false
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=Delete
     *
     * @param string $subscriber_id
     * @param mixed $list_id
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
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

    /**
     * subscriberGet
     * - Returns a subscriber
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=GetSubscriberDetails
     *
     * @param string $subscriber_id
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
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

    /**
     * loadCustomFields
     * - Get a list of fields set on a subscriber, does not use email address
     * - Only returns filled fields
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=LoadSubscriberCustomFields
     *
     * @param int $ext_id MailPlatforms subscriber id
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function loadCustomFields($ext_id)
    {
        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'loadsubscribercustomfields';

        $requestBody = [
            'details' => ['subscriberid' => $ext_id],
            ];

        $request->body = $requestBody;

        return $request->execute();
    }

    /**
     * subscriberActivate
     * - Active (confirm?) a subscriber
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=ActivateSubscriber
     *
     * @param string $subscriber_id
     * @param int $list_id
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
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
        $request->method = 'ActivateSubscriber';
        $request->body   = $requestBody;

        return $request->execute();
    }

    /**
     * subscriberIsSubscribed
     * - Returns mail platform's id
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=IsSubscriberOnList
     *
     * @param string $subscriber_id
     * @param Array $list_ids
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
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

    /**
     * subscriberAddToList
     * - Subscribes a user to a list
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=AddSubscriberToList
     *
     * @param string $subscriber_id
     * @param int $list_id
     * @param array $params
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function subscriberAddToList($subscriber_id, $list_id, Array $params = [])
    {
        $requestBody = [
            'details' => [
                'emailaddress'     => $subscriber_id,
                'mailinglist'      => $list_id,
                'format'           => 'html',
                'confirmed'        => 'false',
                'confirm_language' => 'EN',
                ],
        ];

        $optionalParams = ['format', 'confirmed', 'confirm_language', 'add_to_autoresponders', 'customfields'];

        $requestBody = $this->getOptionalParams($optionalParams, $params, $requestBody);

        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'AddSubscriberToList';
        $request->body   = $requestBody;

        return $request->execute();
    }

    /**
     * subscriberGetSubscribedLists
     * - Finds which lists the email is subscribed to
     * - http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=GetSubscriberDetails
     *
     * @param string $subscriber_id
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
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

    /**
     * listsGet
     * - Find all lists
     * - http://mailmailmail.net/xmlguide/index.php?rt=Lists&rm=GetLists
     *
     * @param Array $params
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
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

    /**
     * getRequest
     * - Build a request for the current domain
     * - Contains a hardcoded list of domainkey -> list settings
     *
     * @return MailPlatformRequest
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function getRequest()
    {
        $domainKey = Hanzo::getInstance()->get('core.domain_key');
        $username  = false;
        $token     = false;

        $baseUrl = 'http://client2.mailmailmail.net';
        $query   = 'xml.php';

        switch ($domainKey)
        {
            case 'SalesDK':
            case 'DK':
                $username  = 'pompdelux_dk';
                $token     = 'c4bfaa0026f352e13aab064ea623e6cec3703e64';
                break;
            case 'COM':
                $username  = 'pompdelux_com';
                $token     = '1dd11f39af9fc74dd63ac74a995dd5d95670f4bb';
                break;
            case 'SalesSE':
            case 'SE':
                $username  = 'pompdelux_se';
                $token     = '0f349587edb8d02f7c6e4308effb736e91536614';
                break;
            case 'SalesNO':
            case 'NO':
                $username  = 'pompdelux_no';
                $token     = '8c53a4b6e3fae2ff4c63a5c8eeecd18c4bcb2d69';
                break;
            case 'SalesNL':
            case 'NL':
                $username  = 'pompdelux_nl';
                $token     = '4c8974d2f2265bb3b1a5b21de6023d3d29832694';
                break;
            case 'SalesFI':
            case 'FI':
                $username  = 'pompdelux_fi';
                $token     = 'c5e1ff20e81df10ba1982c8b5fedd15d964e3390';
                break;
            case 'SalesDE':
            case 'DE':
                $username  = 'pompdelux_de';
                $token     = '7180e33ab06eca29a796e418c24617827e1092f6';
                break;
            case 'SalesAT':
            case 'AT':
                $username  = 'pompdelux_at';
                $token     = '418aa0754954c2fe04d00ee3001747096900f4ad';
                break;
            case 'SalesCH':
            case 'CH':
                $username  = 'pompdelux_ch';
                $token     = 'dd9e68eb8584dc204df3f321557593adb65182d9';
                break;
        }

        $request = new MailPlatformRequest($username, $token, $baseUrl, $query);

        return $request;
    }

    /**
     * getOptionalParams
     * - Checks $params if one of the optional params is set and adds them to requestBody
     *
     * @param array $optionalParams
     * @param array $params
     * @param array $requestBody
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
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
