<?php

namespace Hanzo\Bundle\PaymentBundle;

use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

interface PaymentMethodApiCallInterface
{
    /**
     * Cancels an order
     *
     * @return void
     */
    public function cancel(Customers $customer, Orders $order);
}
