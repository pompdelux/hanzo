<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle;

use Hanzo\Core\Hanzo;

/**
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class NewsletterApi
{
    public $domainKey = null;

    protected $mailer = null;
    protected $cache = null;

    /**
     * @var Providers\BaseProvider
     */
    protected $provider = null;

    /**
     * NewsletterApi constructor.
     *
     * @param $mailer
     * @param $provider
     * @param $cache
     */
    public function __construct( $mailer, $provider, $cache )
    {
        $this->mailer      = $mailer;
        $this->domainKey   = Hanzo::getInstance()->get('core.domain_key');
        $this->provider    = $provider;
        $this->cache       = $cache;
    }

    /**
     * @param string $key
     */
    public function setDomainKey($key)
    {
        $this->domainKey           = $key;
        $this->provider->domainKey = $key;
    }

    /**
     * sendNotificationEmail
     *
     * @deprecated Deprecated as MailPlatform does this by it self
     * @return true
     */
    public function sendNotificationEmail( $action, $email, $name = '' )
    {
        return true;
    }

    /**
     * subscribe
     *
     * @param string $email     An valid e-mail
     * @param mixed  $list_id   int or array of int's accepted
     * @param array  $extraData contains data about the subscriber
     *
     * @return \stdClass
     **/
    public function subscribe($email, $list_id, Array $extraData = [])
    {
        // Error, you can only subscribe to one mailing list at the time
        if (is_array($list_id)) {
            $list_id = array_shift($list_id);
        }

        $response = $this->provider->subscriberCreate($email, $list_id, $extraData);

        // Wrap response in something the rest of the system expects
        $combatibleResponse               = new \stdClass();
        $combatibleResponse->is_error     = $response->isError();
        $combatibleResponse->content      = new \stdClass();
        $combatibleResponse->content->msg = $response->isError() ? $response->getErrorMessage() : 'ok';

        return $combatibleResponse;
    }

    /**
     * @param string $email
     * @param string $list_id
     *
     * @return \stdClass
     */
    public function unsubscribe($email, $list_id)
    {
        if ($list_id === 'ALL') {
            $list_id = false;
        } else {
            if (is_array($list_id)) {
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
     * Also see ConsultantNewsletterApi
     *
     * @return mixed
     **/
    public function getListIdAvaliableForDomain()
    {
        return $this->provider->getDomainListId();
    }

    /**
     * @param string $email
     *
     * @deprecated
     * @return false
     */
    public function getUserByEmail($email) {
        return false;
    }

    /**
     * @param string $email
     * @param string $list_id
     *
     * @deprecated
     * @return false
     */
    public function getSubscriptionStateByEmail($email, $list_id)
    {
        return false;
    }

    /**
     * @param string $email
     *
     * @return \stdClass
     */
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
} // END class NewsletterApi
