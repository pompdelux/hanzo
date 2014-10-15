<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Hanzo\Model\Orders;

class FilterOrderEvent extends Event
{
    protected $order;
    protected $status = null;
    protected $in_edit = false;

    public function __construct(Orders $order)
    {
        $this->order = $order;
        $this->setStatus(true, '');
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getStatusCode()
    {
        return $this->status->code;
    }

    public function getStatusMessage()
    {
        return $this->status->message;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($code, $message = '')
    {
        $this->status = (object) [
            'code'    => $code,
            'message' => $message
        ];

        if (false === $code) {
            $this->stopPropagation();
        }

        return $this;
    }

    public function getInEdit()
    {
        return $this->in_edit;
    }

    public function setInEdit($state = true)
    {
        $this->in_edit = (bool)$state;

        return $this;
    }
}
