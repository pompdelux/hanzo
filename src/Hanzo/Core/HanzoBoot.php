<?php

namespace Hanzo\Core;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class HanzoBoot
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $attr = $event->getRequest()->attributes;
        $attr->set('_request_type', $event->getRequestType());

        // use this to switch between layouts for pc/mobile devices
        $device = 'pc';
        if (isset($_SERVER['HTTP_X_DEVICE'])) {
            $device = $_SERVER['HTTP_X_DEVICE'];
        }
        $attr->set('_x_device', $device);
    }
}
