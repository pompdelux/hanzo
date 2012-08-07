<?php

namespace Hanzo\Core;

use AppKernel;
use Hanzo\Core\Tools;
use Hanzo\Core\RedisCache;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class HanzoBoot
{
    protected $router;
    protected $kernel;

    /**
     * __construct
     *
     * @param Router $router
     */
    public function __construct(Router $router, AppKernel $kernel)
    {
        $this->router = $router;
        $this->kernel = $kernel;
    }


    /**
     * onKernelRequest events
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->deviceCheck($event);
        $this->webshopAccessRestrictionCheck($event);
    }


    /**
     * device check is used to allow us to toggle layout based on the device type
     *
     * @param GetResponseEvent $event
     */
    protected function deviceCheck(GetResponseEvent $event)
    {
        $attr = $event->getRequest()->attributes;
        $attr->set('_request_type', $event->getRequestType());
        $attr->set('_x_device', 'pc');
    }


    /**
     * webshop access restriction check
     *
     * @param GetResponseEvent $event
     */
    protected function webshopAccessRestrictionCheck(GetResponseEvent $event)
    {
        $hanzo = \Hanzo\Core\Hanzo::getInstance();

        if (1 == $hanzo->get('webshop.closed', 0)) {
            $request = $event->getRequest();

            list($uri,) = explode('?', $request->getRequestUri());
            $clean = str_replace('//', '/', str_replace('app_dev.php', '', $uri));
            $params = $this->router->match($clean);

            // if the route is ip restricted, redirect if not from an approved ip
            if (isset($params['ip_restricted']) && $params['ip_restricted'] == 1 ) {
                $ips = explode("\n", str_replace("\r", '', $hanzo->get('webshop.closed.allowed_ips', '')));

                if (!in_array($request->getClientIp(), $ips)) {
                    $goto = '/';
                    $env = $hanzo->get('core.env');

                    if ( !in_array( $env, array( 'prod', 'prod_dk_consultant' ) ) ) {
                        $goto = '/app_'.$env.'.php';
                    }

                    $hanzo->container->get('session')->setFlash('notice', 'access.denied');
                    header('Location: '.$goto); exit;
                }
            }
        }
    }


    /**
     * handles device cookie setting
     *
     * @param string $device name of the device type
     */
    protected static function setDeviceCookie($device)
    {
        if (isset($_COOKIE['_x_device']) && $_COOKIE['_x_device'] == $device) {
            return;
        }

        setcookie("_x_device", $device, 0, '/', '', false, true);
    }
}
