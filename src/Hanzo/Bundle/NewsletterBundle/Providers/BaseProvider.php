<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

/**
 * Class BaseProvider
 *
 * @author Henrik Farre <hf@bellcom.dk>
 * @package Hanzo\Bundle\NewsletterBundle\Providers
 */
abstract class BaseProvider
{
    /**
     * subscriberCreate
     *
     * @param string $subscriber_id
     * @param array $params
     *
     * @return BaseResponse
     */
    abstract public function subscriberCreate($subscriber_id, $list_id, Array $params = []);

    /**
     * subscriberUpdate
     *
     * @param string $subscriber_id
     * @param array $params
     *
     * @return BaseResponse
     */
    abstract public function subscriberUpdate($subscriber_id, $list_id, Array $params = []);

    /**
     * subscriberDelete
     *
     * @param string $subscriber_id
     * @param int $list_id
     * @param array $params
     *
     * @return BaseResponse
     */
    abstract public function subscriberDelete($subscriber_id, $list_id, Array $params = []);

    /**
     * subscriberGet
     *
     * @param string $subscriber_id
     *
     * @return BaseResponse
     */
    abstract public function subscriberGet($subscriber_id);

    /**
     * subscriberActivate
     *
     * @param string $subscriber_id
     * @param int $list_id
     *
     * @return BaseResponse
     */
    abstract public function subscriberActivate($subscriber_id, $list_id);

    /**
     * subscriberIsSubscribed
     *
     * @param string $subscriber_id
     * @param array $list_ids
     *
     * @return BaseResponse
     */
    abstract public function subscriberIsSubscribed($subscriber_id, Array $list_ids);

    /**
     * subscriberAddToList
     *
     * @param string $subscriber_id
     * @param int $list_id
     * @param array $params
     *
     * @return BaseResponse
     */
    abstract public function subscriberAddToList($subscriber_id, $list_id, Array $params = []);

    /**
     * subscriberGetSubscribedLists
     *
     * @param string $subscriber_id
     *
     * @return BaseResponse
     */
    abstract public function subscriberGetSubscribedLists($subscriber_id);

    /**
     * listsGet
     *
     * @param array $params
     *
     * @return BaseResponse
     */
    abstract public function listsGet(Array $params = []);

    /**
     * Get list id for the current domain, if any.
     * 
     * @return mixed
     */
    public function getDomainListId()
    {
        if (!empty($this->domainToListMap) && !empty($this->domanKey)) {
            $key = str_replace('sales', '', strtolower($this->domanKey));
            if (isset($this->domainToListMap[$key])) {
                return $this->domainToListMap[$key];
            }
        }

        return 0;
    }
}
