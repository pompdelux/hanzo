<?php

namespace Hanzo\Core;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class HanzoBoot
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $event->getRequest()->attributes->set('_request_type', $event->getRequestType());
    }
}
