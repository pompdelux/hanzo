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
}
