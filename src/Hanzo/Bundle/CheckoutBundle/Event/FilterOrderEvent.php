<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Hanzo\Model\Orders;

class FilterOrderEvent extends Event
{
    protected $order;

    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }
}
