<?php

namespace Hanzo\Bundle\GoogleBundle\DataLayer;

abstract class AbstractDataLayer
{
    protected $data = [];

    abstract public function __construct($page_type = '', Array $context = [], Array $params = []);

    public function getData()
    {
        return $this->data;
    }
}
