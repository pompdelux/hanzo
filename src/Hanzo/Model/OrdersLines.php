<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseOrdersLines;

class OrdersLines extends BaseOrdersLines
{
    public function setPrice($v)
    {
        return parent::setPrice(round($v, 2));
    }

    public function setOriginalPrice($v)
    {
        return parent::setOriginalPrice(round($v, 2));
    }
}
