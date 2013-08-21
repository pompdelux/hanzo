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
     * Called when an order is successfull completed
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderSuccess(Request $request, Orders $order);

    /**
     * updateOrderFailed
     * Called when an order failed
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderFailed(Request $request, Orders $order);

    /**
     * getFee
     * Returns the fee for the current payment method, or 0 if none
     * @param string The method to retrive for
     * @return float
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFee($method = NULL);

    /**
     * getFeeExternalId
     * Returns the id used by the fee for AX, or null if there is no fee
     * @return mixed
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFeeExternalId();

    /**
     * Return a form or an goto url for the process button
     *
     * @return array
     */
    public function getProcessButton(Orders $order, Request $request);
}
