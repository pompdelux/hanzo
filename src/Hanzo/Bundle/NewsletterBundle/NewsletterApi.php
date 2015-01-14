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
    protected $phplistUrl  = null;
    protected $httpReferer = null;
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
        $this->phplistUrl  = 'http://phplist.pompdelux.dk/';
        $this->httpReferer = 'http://www.pompdelux.dk/';
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
     * @param string $email An valid e-mail
     * @param mixed $listid int or array of int's accepted
     * @return stdClass
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function subscribe( $email, $list_id  )
    {
        if (is_array($list_id))
        {
            // Error, you can only subscribe to one mailing list at the time
            $list_id = array_shift($list_id);
        }

        $response = $this->provider->subscriberCreate($email, $list_id);

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

        // TODO: priority: low, hardcoded vars
        switch ($this->domainKey)
        {
            case 'SalesDK':
            case 'DK':
                $listid = 2002;
                break;
            case 'COM':
                $listid = 2;
                break;
            case 'SalesSE':
            case 'SE':
                $listid = 4;
                break;
            case 'SalesNO':
            case 'NO':
                $listid = 5;
                break;
            case 'SalesNL':
            case 'NL':
                $listid = 20;
                break;
            case 'SalesFI':
            case 'FI':
                $listid = 30;
                break;
            case 'SalesDE':
            case 'DE':
                $listid = 53;
                break;
            case 'SalesAT':
            case 'AT':
                $listid = 54;
                break;
            case 'SalesCH':
            case 'CH':
                $listid = 55;
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
        $cache_id = $this->cache->generateKey([__CLASS__,__FUNCTION__]);

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

} // END class NewsletterApi
