<?php

namespace Hanzo\Bundle\GoogleBundle\DataLayer;

class Remarketing extends AbstractDataLayer
{
    public function __construct($page_type = '', Array $context = [], Array $params = [])
    {
        if ($page_type != 'checkout-success') {
            return;
        }
    }
}
