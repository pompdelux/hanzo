<?php

namespace Hanzo\Bundle\PaymentBundle;

use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Request;

interface PaymentMethodApiInterface
{
    /**
     * isActive
     * @return boolean Returns if the method is active
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function isActive();

    /**
     * call
     * Calls the underlying ApiCall method
     * Example: 
     *  $api = $this->get('payment.dibsapi');
     *  $api->call()->cancel();
     * @return mixed
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function call();

    /**
     * updateOrderSuccess
     * Called when an order is success full completed
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order );
}
