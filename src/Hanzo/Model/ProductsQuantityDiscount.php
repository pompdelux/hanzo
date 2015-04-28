<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseProductsQuantityDiscount;


/**
 * Skeleton subclass for representing a row from the 'products_quantity_discount' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.src.Hanzo.Model
 */
class ProductsQuantityDiscount extends BaseProductsQuantityDiscount {

    /**
     * @param mixed $value
     */
    public function setDiscount($value)
    {
        $value = str_replace(',', '.', $value);
        return parent::setDiscount($value);
    }
} // ProductsQuantityDiscount
