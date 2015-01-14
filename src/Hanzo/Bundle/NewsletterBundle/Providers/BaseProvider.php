<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

abstract class BaseProvider
{
    /**
     * subscriberCreate
     *
     * @param string $subscriber_id
     * @param array $params
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberCreate($subscriber_id, $list_id, Array $params = []);

    /**
     * subscriberUpdate
     *
     * @param string $subscriber_id
     * @param array $params
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberUpdate($subscriber_id, $list_id, Array $params = []);

    /**
     * subscriberDelete
     *
     * @param string $subscriber_id
     * @param int $list_id
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberDelete($subscriber_id, $list_id);

    /**
     * subscriberGet
     *
     * @param string $subscriber_id
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberGet($subscriber_id);

    /**
     * subscriberActivate
     *
     * @param string $subscriber_id
     * @param int $list_id
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberActivate($subscriber_id, $list_id);



    /**
     * subscriberIsSubscribed
     *
     * @param string $subscriber_id
     * @param array $list_ids
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberIsSubscribed($subscriber_id, Array $list_ids);

    /**
     * subscriberAddToList
     *
     * @param string $subscriber_id
     * @param int $list_id
     * @param array $params
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberAddToList($subscriber_id, $list_id, $params = []);

    /**
     * subscriberGetSubscribedLists
     *
     * @param string $subscriber_id
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberGetSubscribedLists($subscriber_id);



    /**
     * listsGet
     *
     * @param array $params
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function listsGet(Array $params = []);
}
