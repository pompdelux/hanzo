<?php

namespace Hanzo\Bundle\PaymentBundle;

use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

/**
 * Interface PaymentMethodApiCallInterface
 *
 * @package Hanzo\Bundle\PaymentBundle
 */
interface PaymentMethodApiCallInterface
{
    /**
     * Cancels an order
     *
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return mixed
     */
    public function cancel(Customers $customer, Orders $order);
}
