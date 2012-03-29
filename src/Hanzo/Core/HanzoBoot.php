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

        if (isset($_GET['_x_device'])) {
            if (preg_match('/[a-z]+/i', $_GET['_x_device'])) {
                $device = $_GET['_x_device'];
            } else {
                unset($_GET['_x_device']);
            }
        }

        if (isset($_COOKIE['_x_device'])) {
            if (isset($_GET['_x_device']) && ($_COOKIE['_x_device'] != $device)) {
                self::setDeviceCookie($device);
            } else {
                $device = $_COOKIE['_x_device'];
            }
        } else {
            self::setDeviceCookie($device);
        }

        $attr->set('_x_device', $device);
    }

    protected static function setDeviceCookie($device)
    {
        setcookie("_x_device", $device, 0, '/', '', false, true);
    }
}
