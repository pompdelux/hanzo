<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

abstract class BaseProvider
{

    /**
     * subscriberCreate
     *
     * @param mixed $subscriber_id
     * @param array $params
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    abstract public function subscriberCreate($subscriber_id, $params);
    abstract public function subscriberUpdate($subscriber_id, $params);
    abstract public function subscriberDelete($subscriber_id);
    abstract public function subscriberGet($subscriber_id);
    abstract public function subscriberActivate($subscriber_id);

    abstract public function subscriberIsSubscribed($subscriber_id, $list_ids);
    abstract public function subscriberAddToList($subscriber_id, $list_id);
    abstract public function subscriberGetSubscribedLists($subscriber_id);

    abstract public function listsGet($params);
}
