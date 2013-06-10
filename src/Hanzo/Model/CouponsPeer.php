<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseCouponsPeer;

use Hanzo\Model\CouponsQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'coupons' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class CouponsPeer extends BaseCouponsPeer
{

    /**
     * Generate a random coupon code
     *
     * @param  integer $length Length of the code
     * @param  string  $prefix Code prefix, note this will not count as part of the length
     * @return string
     */
    public static function generateCode($length = 9, $prefix = '')
    {
        // make sure we have a uniq code for every coupon
        while (true) {
            $code = $prefix.substr(str_shuffle(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 2))), 0, $length);

            if (0 == CouponsQuery::create()->filterByCode($code)->count()) {
                return $code;
            }
        }
    }

} // CouponsPeer
