<?php

namespace Hanzo\Bundle\VarnishBundle\Event;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\EventDispatcher\Event as FilterEvent;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Hanzo\Bundle\VarnishBundle\Varnish;

use Hanzo\Core\Tools;

class BanListener
{
    protected $locale;
    protected $varnish;
    protected $router;

    public function __construct(Varnish $varnish, Router $router, $locale)
    {
        $this->varnish = $varnish;
        $this->router = $router;
        $this->locale = $locale;
    }

    public function onBanCmsNode(FilterEvent $event)
    {
        $object = $event->getData();
Tools::log($object);
    }
}
