<?php

namespace Hanzo\Bundle\PaymentBundle;

interface PaymentMethodApiCallInterface
{
  /**
   * cancel
   * Cancels an order
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function cancel( Customers $customer, Orders $order );
}
