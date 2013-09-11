<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseOrdersLines;

class OrdersLines extends BaseOrdersLines
{
    public function setPrice($v)
    {
        return parent::setPrice(number_format($v, 2, '.', ''));
    }

    public function setOriginalPrice($v)
    {
        return parent::setOriginalPrice(number_format($v, 2, '.', ''));
    }
}
