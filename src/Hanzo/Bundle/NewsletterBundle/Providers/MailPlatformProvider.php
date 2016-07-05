<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

use Hanzo\Core\Hanzo;

class MailPlatformProvider extends BaseProvider
{
    /**
     * Contains a hardcode list of domainkey -> listid relations
     *
     * @var array
     */
    protected $domainToListMap = [
        'dk'  => 2002,
        'com' => 2056,
        'se'  => 2054,
        'no'  => 2053,
        'nl'  => 2048,
        'fi'  => 2047,
        'de'  => 2045,
        'at'  => 2042,
        'ch'  => 2044,
    ];

    public function __construct()
    {
        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');
    }

    /**
     * subscriberCreate
     *
     * @see subscriberAddToList
     *
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function subscriberCreate($subscriber_id, $list_id, Array $params = [])
    {
        // Map external data to something the provider understands
        $params = $this->mapExtraDataToProvider($params);

        $language = $this->getLanguageForMails();
        $params['confirm_language'] = $language;

        // If subscriber is unsubscribed (not deleted) we have to activate the subscriber first - else just create
        $response = $this->subscriberActivate($subscriber_id, $list_id);
        if ($response->getStatus() === BaseResponse::REQUEST_SUCCESS) {
            return $response;
        }
        else
        {
            return $this->subscriberAddToList($subscriber_id, $list_id, $params);
        }
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
     * - NOTE: uses UnsubscribeSubscriber and replaces old delete function
     *
     * - Remove a subscriber from a list, or all if list_id = false
     * - http://www.mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=UnsubscribeSubscriber
     *
     * @param string $subscriber_id
     * @param mixed  $list_id
     * @param array  $params
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function subscriberDelete($subscriber_id, $list_id = false, Array $params = [])
    {
        $language = $this->getLanguageForMails();
        $params = [];
        $params['language'] = $language;

        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'UnsubscribeSubscriber';

        $requestBody = [
            'details' => [
                'emailaddress' => $subscriber_id,
                'sendthankyou' => 'true',
                'language'     => 'EN',
                'formid'       => $this->getUnsubscribeFormId($list_id),
            ],
        ];

        if ($list_id !== false)
        {
            $requestBody['details']['listid'] = $list_id;
        }

        $optionalParams = ['sendthankyou', 'language'];

        $requestBody = $this->getOptionalParams($optionalParams, $params, $requestBody);

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
                'emailaddress'          => $subscriber_id,
                'mailinglist'           => $list_id,
                'format'                => 'html',
                'confirmed'             => 'false',
                'confirm_language'      => 'EN',
                'add_to_autoresponders' => 1,
            ],
        ];

        $optionalParams = ['format', 'confirmed', 'confirm_language', 'add_to_autoresponders', 'customfields'];

        $requestBody = $this->getOptionalParams($optionalParams, $params, $requestBody);

        $request         = $this->getRequest();
        $request->type   = 'subscribers';
        $request->method = 'AddSubscriberToList';
        $request->body   = $requestBody;
        // Run using beanstalk
        $request->async  = true;

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
        $username  = false;
        $token     = false;

        $baseUrl = 'http://client2.mailmailmail.net';
        $query   = 'xml.php';

        switch ($this->domainKey)
        {
            case 'SalesDK':
            case 'DK':
                $username  = 'pompdelux_dk';
                $token     = '1d038106bb2575a0bc5c469427fa8ffe72ff39e7';
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

    /**
     * getUnsubscribeFormId
     * - Maps list id to unsubscribe form id
     * - Find ids on this page http://client2.mailmailmail.net/admin/index.php?Page=Forms
     *
     * @param string $listId
     *
     * @return int
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function getUnsubscribeFormId($listId)
    {
        $formId = 499;

        switch ($listId)
        {
            case 2002: // 'DK'
                $formId = 471;
                break;
            case 2054: // 'SE'
                $formId = 493;
                break;
            case 2053: // 'NO'
                $formId = 491;
                break;
            case 2048: // 'NL'
                $formId = 487;
                break;
            case 2047: // 'FI'
                $formId = 485;
                break;
            case 2045: // 'DE'
                $formId = 483;
                break;
            case 2042: // 'AT'
                $formId = 479;
                break;
            case 2044: // 'CH'
                $formId = 481;
                break;
            default:
            case 2056: // 'COM'
                $formId = 499;
                break;
        }

        return $formId;
    }


    /**
     * mapExtraDataToProvider
     *
     * @param array $data
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function mapExtraDataToProvider($data)
    {
        $params = [];

        // TODO: Dropdown/date is not supported as it requires a sub key element
        // see http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=Update

        // Comment is: fieldtype, fieldname, description of content
        $knownFields = [
            'title'           => 1,   // dropdown, Title,
            'first_name'      => 2,   // text, First Name
            'last_name'       => 3,   // text, Last Name
            'phone'           => 4,   // text, Phone
            'mobile'          => 5,   // text, Mobile
            'fax'             => 6,   // text, Fax
            'birthdate'       => 7,   // date, Birth Date
            'city'            => 8,   // text, City
            'state'           => 9,   // text, State
            'zip_code'        => 10,  // text, Postal/Zip Code
            'country'         => 11,  // dropdown, Country
            'name'            => 944, // txt, Navn
            'barn_1'          => 939, // radiobutton, Barn 1, pige/dreng
            'barn_1_bday'     => 933, // date, Barn 1 fødselsdag
            'barn_2'          => 940, // radiobutton, Barn 1, pige/dreng
            'barn_2_bday'     => 937, // date, Barn 1 fødselsdag
            'barn_3'          => 938, // radiobutton, Barn 3, pige/dreng
            'barn_3_bday'     => 941, // date, Barn 3 fødselsdag
            'barn_4'          => 942, // radiobutton, Barn 3, pige/dreng
            'barn_4_bday'     => 943, // date, Barn 3 fødselsdag
            'email_frequency' => 921, // radiobutton, Email frequency
        ];

        /**
         * Creates an array that looks like this:
         *
         * $params = [
         *     'customfields' => [
         *         'item' => [
         *             ['2', 'tester'],
         *             ['3', 'tester'],
         *         ]
         *         ],
         *     ];
         *
         *  The first element in item is the field id in MailPlatform, the other is the value
         *
         */
        foreach ($knownFields as $fieldName => $fieldId)
        {
            if (isset($data[$fieldName]))
            {
                $params['customfields']['item'][] = [$fieldId, $data[$fieldName]];
            }
        }

        return $params;
    }

    /**
     * getLanguageForMails
     *
     *
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function getLanguageForMails()
    {
        // Set language for confirmation mail
        $language = 'EN';
        // EN, DK, DE, NO, SE

        switch ($this->domainKey)
        {
            case 'SalesDK':
            case 'DK':
                $language = 'DK';
                break;
            case 'COM':
                $language = 'EN';
                break;
            case 'SalesSE':
            case 'SE':
                $language = 'SE';
                break;
            case 'SalesNO':
            case 'NO':
                $language = 'NO';
                break;
            case 'SalesNL':
            case 'NL':
                $language = 'EN';
                break;
            case 'SalesFI':
            case 'FI':
                $language = 'EN';
                break;
            case 'SalesDE':
            case 'DE':
                $language = 'DE';
                break;
            case 'SalesAT':
            case 'AT':
                $language = 'DE';
                break;
            case 'SalesCH':
            case 'CH':
                $language = 'EN';
                break;
        }

        return $language;
    }
}
