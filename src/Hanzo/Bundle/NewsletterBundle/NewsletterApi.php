<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools
    ;

/**
 * undocumented class
 *
 * @packaged default
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class NewsletterApi
{
    protected $domainKey   = null;
    protected $mailer      = null;
    protected $provider    = null;
    protected $cache       = null;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $mailer, $provider, $cache )
    {
        // TODO: priority: low, hardcoded vars
        $this->mailer      = $mailer;
        $this->domainKey   = Hanzo::getInstance()->get('core.domain_key');
        $this->provider    = $provider;
        $this->cache       = $cache;
    }

    /**
     * sendNotificationEmail
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function sendNotificationEmail( $action, $email, $name = '' )
    {
        // Deprecated as MailPlatform does this by it self
        return true;
    }

    /**
     * subscribe
     *
     * @param string $email An valid e-mail
     * @param mixed $listid int or array of int's accepted
     * @param array $extraData contains data about the subscriber
     *
     * @return stdClass
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function subscribe( $email, $list_id, Array $extraData = [] )
    {
        if (is_array($list_id))
        {
            // Error, you can only subscribe to one mailing list at the time
            $list_id = array_shift($list_id);
        }

        // Map external data to something the provider understands
        $params = $this->mapExtraDataToProvider($extraData);

        $response = $this->provider->subscriberCreate($email, $list_id, $params);

        // Wrap response in something the rest of the system expects
        $combatibleResponse               = new \stdClass();
        $combatibleResponse->is_error     = $response->isError();
        $combatibleResponse->content      = new \stdClass();
        $combatibleResponse->content->msg = $response->isError() ? $response->getErrorMessage() : 'ok';

        return $combatibleResponse;
    }


    public function unsubscribe($email, $list_id)
    {
        if ($list_id === 'ALL')
        {
            $list_id = false;
        }
        else
        {
            if (is_array($list_id))
            {
                // Error, you can only subscribe to one mailing list at the time
                $list_id = array_shift($list_id);
            }
        }

        $response = $this->provider->subscriberDelete($email, $list_id);

        // Wrap response in something the rest of the system expects
        $combatibleResponse               = new \stdClass();
        $combatibleResponse->is_error     = $response->isError();
        $combatibleResponse->content      = new \stdClass();
        $combatibleResponse->content->msg = $response->isError() ? $response->getErrorMessage() : 'ok';

        return $combatibleResponse;
    }

    /**
     * getListIdAvaliableForDomain
     *
     * Contains a hardcode list of domainkey -> listid relations
     *
     * Also see ConsultantNewsletterApi
     *
     * @return int
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getListIdAvaliableForDomain()
    {
        $listid = 0;

        switch ($this->domainKey)
        {
            case 'SalesDK':
            case 'DK':
                $listid = 2002;
                break;
            case 'COM':
                $listid = 2056;
                break;
            case 'SalesSE':
            case 'SE':
                $listid = 2054;
                break;
            case 'SalesNO':
            case 'NO':
                $listid = 2053;
                break;
            case 'SalesNL':
            case 'NL':
                $listid = 2048;
                break;
            case 'SalesFI':
            case 'FI':
                $listid = 2047;
                break;
            case 'SalesDE':
            case 'DE':
                $listid = 2045;
                break;
            case 'SalesAT':
            case 'AT':
                $listid = 2042;
                break;
            case 'SalesCH':
            case 'CH':
                $listid = 2044;
                break;
        }

        return $listid;
    }


    public function getUserByEmail($email) {
        // Deprecated
        return false;
    }

    public function getSubscriptionStateByEmail($email, $list_id)
    {
        // Deprecated
        return false;
    }

    public function getAllLists($email)
    {
        // is_subscribed is not set, so $email is ignored
        $cache_id = $this->cache->generateKey([__METHOD__]);

        $cacheResult = $this->cache->get($cache_id);

        if (is_object($cacheResult))
        {
            return $cacheResult;
        }

        $response = $this->provider->listsGet();
        $data = $response->getData();

        // Wrap response in something the rest of the system expects
        $combatibleResponse                 = new \stdClass();
        $combatibleResponse->is_error       = $response->isError();
        $combatibleResponse->content        = new \stdClass();
        $combatibleResponse->content->msg   = $response->isError() ? $response->getErrorMessage() : 'ok';
        $combatibleResponse->content->lists = [];

        foreach ($data['list_info'] as $list)
        {
            $mapped                  = [];
            $mapped['id']            = $list['listid'];
            $mapped['name']          = $list['name'];
            $mapped['description']   = $list['name'];
            $mapped['entered']       = date('Y-m-d H:i:s', $list['createdate']);
            $mapped['listorder']     = 0;
            $mapped['prefix']        = '';
            $mapped['rssfeed']       = '';
            $mapped['modified']      = date('Y-m-d H:i:s', $list['createdate']);
            $mapped['active']        = 1;
            $mapped['owner']         = $list['ownerid'];
            $mapped['is_subscribed'] = false;

            $combatibleResponse->content->lists[] = (object)$mapped;
        }

        // Why is this needed?
        $reindexed = [];
        foreach ($combatibleResponse->content->lists as $list) {
            if ('pdl_' == substr(strtolower($list->name), 0, 4)) {
                continue;
            }

            $reindexed[$list->id] = $list;
        }

        $combatibleResponse->content->lists = $reindexed;

        $this->cache->set($cache_id, $combatibleResponse);

        return $combatibleResponse;
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

} // END class NewsletterApi
