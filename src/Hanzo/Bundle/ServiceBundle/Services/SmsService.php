<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class SmsService
{
    protected $parameters;
    protected $settings;

    public function __construct($parameters, $settings)
    {
        $this->parameters = $parameters;
        $this->settings = $settings;
    }
}
