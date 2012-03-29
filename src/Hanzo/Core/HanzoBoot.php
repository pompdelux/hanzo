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
        if (isset($_GET['_x_device']) && preg_match('/[a-z]+/i', $_GET['_x_device'])) {
            $device = $_GET['_x_device'];
        }

        if (isset($_COOKIE['_x_device'])) {
            if ($_COOKIE['_x_device'] != $device) {
                $_COOKIE['_x_device'] = $device;
            }
        } else {
            $_COOKIE['_x_device'] = $device;
        }

        $attr->set('_x_device', $device);
    }
}
