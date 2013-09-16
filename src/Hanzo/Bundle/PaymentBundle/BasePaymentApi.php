<?php

namespace Hanzo\Bundle\PaymentBundle;

class BasePaymentApi
{
    /**
     * Get the name of the api
     *
     * @return string
     */
    final public function getApiName()
    {
        return basename(str_replace('\\', '/', strtolower(get_class($this))));
    }


    /**
     * Get available paytypes
     *
     * @return mixed Returns null if no paytypes are available
     */
    public function getPayTypes()
    {
        return null;
    }

    /**
     * Get the fee for this payment.
     *
     * @param  string Method
     * @return float
     */
    public function getFee($method = NULL)
    {
        // Fee defined for specific method in settings.
        if (isset($this->settings[$method . '-fee'])) {
            return $this->settings[$method . '-fee'];
        }

        // Default fee for module, or zero.
        return ( isset($this->settings['fee']) ) ? $this->settings['fee'] : 0.00;
    }

    /**
     * Get the order in which the method should be displayed.
     * @return int An integer to order the payment from. NULL if none.
     */
    public function getOrder()
    {
        return ( isset($this->settings['order']) ) ? $this->settings['order'] : NULL;
    }
}
