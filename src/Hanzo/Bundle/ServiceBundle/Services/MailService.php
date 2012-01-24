<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Swift_Mailer;

class MailService
{
    protected $mailer, $twig, $settings;

    public function __construct($parameters, $settings)
    {
        if ($parameters[0] instanceof Swift_Mailer) {
            $this->mailer = $parameters[0];
        }
        else {
            throw new \InvalidArgumentException('Swift_Mailer instance required.');
        }

        if ($parameters[1] instanceof TwigEngine) {
            $this->twig = $parameters[0];
        }
        else {
            throw new \InvalidArgumentException('TwigEngine instance required.');
        }

        $this->settings = $settings;
    }
}
