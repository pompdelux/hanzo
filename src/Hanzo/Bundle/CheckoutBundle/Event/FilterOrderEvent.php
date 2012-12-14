<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Hanzo\Model\Orders;

class FilterOrderEvent extends Event
{
    protected $order;
    protected $status = null;

    public function __construct(Orders $order)
    {
        $this->order = $order;
        $this->setStatus(true, '');
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setStatus($code, $message = '')
    {
        $this->status = (object) [
          'code' => $code,
          'message' => 'message'
        ];

        if (false === $code) {
          $this->stopPropagation();
        }
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
}
