<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\PaymentBundle\Methods\PayPal;

class PayPallDummyCallResponse
{
    public function isError()
    {
        return false;
    }

    public function getError()
    {
        return '';
    }

    public function debug()
    {
        return [
            'headers'            => [],
            'raw_response'       => '',
            'reason'             => '',
            'status'             => true,
            'status_description' => '',
            'status_is_error'    => false,
        ];
    }
}
