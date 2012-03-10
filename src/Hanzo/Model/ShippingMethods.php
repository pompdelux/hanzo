<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseShippingMethods;


/**
 * Skeleton subclass for representing a row from the 'shipping_methods' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.src.Hanzo.Model
 */
class ShippingMethods extends BaseShippingMethods
{
    const TYPE_FEE = true;
    const TYPE_NORMAL = false;

    /**
     * get name of the shipping method
     *
     * @return string
     */
    public function getName()
    {
        return trim($this->getCarrier() . ' ' . $this->getMethod());
    }


} // ShippingMethods
