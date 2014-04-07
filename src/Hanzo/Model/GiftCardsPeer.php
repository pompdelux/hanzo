<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseGiftCardsPeer;
use Hanzo\Model\GiftCardsQuery;

class GiftCardsPeer extends BaseGiftCardsPeer
{

    /**
     * Generate a random coupon code
     *
     * @param  integer $length Length of the code
     * @param  string  $prefix Code prefix, note this will not count as part of the length
     * @param  PropelPDO|null
     * @return string
     */
    public static function generateCode($length = 9, $prefix = '', $con = null)
    {
        // make sure we have a unique code for every coupon
        while (true) {
            $code = $prefix.substr(str_shuffle(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 2))), 0, $length);

            if (0 == GiftCardsQuery::create()->filterByCode($code)->count($con)) {
                return $code;
            }
        }
    }
}
