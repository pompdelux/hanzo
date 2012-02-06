<?php

namespace Hanzo\Bundle\NewsletterBundle;

use Symfony\Component\EventDispatcher\Event;

class FilterTestEvent extends Event
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
