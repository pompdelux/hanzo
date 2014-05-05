<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseCoupons;


/**
 * Skeleton subclass for representing a row from the 'coupons' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class Coupons extends BaseCoupons {

    protected $quantity;

    const TYPE_AMOUNT     = 'amount';
    const TYPE_PERCENTAGE = 'pct';

    public function setQuantity($v)
    {
        $this->quantity = $v;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

} // Coupons
