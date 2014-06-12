<?php

namespace Hanzo\Core;

use AppKernel;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\RedisCache;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\SecurityContextInterface;

class HanzoBoot
{
    protected $router;
    protected $kernel;
    protected $securityContext;

    /**
     * __construct
     *
     * @param SecurityContextInterface $securityContext
     * @param Router                   $router
     * @param AppKernel                $kernel
     */
    public function __construct(SecurityContextInterface $securityContext, Router $router, AppKernel $kernel)
    {
        $this->securityContext = $securityContext;
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
        $response = $this->adminAccessCheck($event);
        if ($response instanceof RedirectResponse) {
            return $event->setResponse($response);
        }

        $this->deviceCheck($event);
        $this->webshopAccessRestrictionCheck($event);

        $container = $this->kernel->getContainer();

        if ($container->hasParameter('video_cdn')) {
            $video_cdn = $container->getParameter('video_cdn');
        } else {
            $video_cdn = $container->getParameter('cdn');
        }

        $container->get('twig')->addGlobal('video_cdn', str_replace(['http:', 'https:'], '', $video_cdn));
    }


    /**
     * device check is used to allow us to toggle layout based on the device type
     *
     * @param GetResponseEvent $event
     */
    protected function deviceCheck(GetResponseEvent $event)
    {
        $container = $this->kernel->getContainer();
        $request   = $event->getRequest();
        $attr      = $request->attributes;

        $attr->set('_request_type', $event->getRequestType());

        if ($request->headers->has('x-ua-device')) {
            $device = $request->headers->get('x-ua-device');
        } else {
            $device = 'pc';
            if ($container->hasParameter('x_ua_device') &&
                $container->getParameter('x_ua_device')
            ) {
                $device = $container->getParameter('x_ua_device');
            }
        }

        $attr->set('_x_device', $device);

        $theme = $container->get('liip_theme.active_theme');
        // set theme to active name + '_mobile' ex: '2013s1_mobile'
        if (preg_match('/^mobile/i', $device) && !preg_match('/mobile/', $theme->getName())) {
            $theme->setName($theme->getName().'_mobile');
        }
    }


    /**
     * webshop access restriction check
     *
     * @param GetResponseEvent $event
     */
    protected function webshopAccessRestrictionCheck(GetResponseEvent $event)
    {
        if ($event->getRequest()->attributes->has('admin_enabled')) {
            return;
        }

        $hanzo = Hanzo::getInstance();

        if (1 == $hanzo->get('webshop.closed', 0)) {
            $request = $event->getRequest();

            // allow edits of event orders.
            // TODO: must be more generic
            if (isset($_COOKIE['__ice'])) {
                return;
            }

            list($uri,) = explode('?', $request->getRequestUri());
            $clean = str_replace('//', '/', str_replace('app_dev.php', '', $uri));
            $params = $this->router->match($clean);

            $blacklist = [
                'basket_view',
                'ws_stock',
                'basket_add',
                '_checkout',
            ];

            if ($params && isset($params['_route']) && in_array($params['_route'], $blacklist)) {
                $params['ip_restricted'] = 1;
            }

            // if the route is ip restricted, redirect if not from an approved ip
            if (isset($params['ip_restricted']) && $params['ip_restricted'] == 1 ) {
                $ips = explode("\n", str_replace("\r", '', $hanzo->get('webshop.closed.allowed_ips', '')));

                if (!in_array($request->getClientIp(), $ips)) {
                    $goto = '';
                    $env = explode('_', $hanzo->get('core.env'));

                    if (!in_array($env[0], array('prod'))) {
                        $goto = '/app_'.$env[0].'.php';
                    }

                    $goto .= '/'.$params['_locale'].'/';

                    $hanzo->container->get('session')->getFlashBag()->add('notice', 'access.denied');
                    header('Location: '.$goto); exit;
                }
            }
        }
    }


    /**
     * @param  GetResponseEvent $event
     * @return RedirectResponse|null
     */
    protected function adminAccessCheck(GetResponseEvent $event)
    {
        $request      = $event->getRequest();
        $request_uri  = $request->getRequestUri();
        $request_type = $event->getRequestType();

        if ((substr($request->getHttpHost(), 0, 6) !== 'admin.')) {
            // if a user tries to access the admin section via the website, redirect the user to the admin. domain
            if (preg_match('~^/[a-z]{2}_[a-z]{2}/admin/~i', $request_uri)) {
                return new RedirectResponse(str_replace('www.', 'admin.', $this->router->generate('admin', ['_locale' => $request->getLocale()], true)));
            }

            return;
        }

        $request->attributes->set('admin_enabled', true);

        // we do not care about requests for resources or sub requests
        if (preg_match('~/[a-z]{2}_[A-Z]{2}/(_wdt|i18n/js)~', $request_uri) ||
            (HttpKernelInterface::SUB_REQUEST === $request_type)
        ) {
            return;
        }

        $login_url = $this->router->generate('admin_login');
        if (($request_uri !== $login_url) &&
            (($this->securityContext->getToken() === '') ||
             ($this->securityContext->getToken()->getUser() === 'anon.')
            )
        ) {
            return new RedirectResponse($login_url);
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

        Tools::setCookie("_x_device", $device, 0, true);
    }
}
