<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseOrdersStateLog;


/**
 * Skeleton subclass for representing a row from the 'orders_state_log' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.src.Hanzo.Model
 */
class OrdersStateLog extends BaseOrdersStateLog
{
    public function info($order_id, $state)
    {
        $this->setOrdersId($order_id);
        // fake neg state to prevent dubs.
        $this->setState(((strlen($state)+100)* -1));
        $this->setMessage($state);
        $this->setCreatedAt(time());

        return $this;
    }
} // OrdersStateLog
