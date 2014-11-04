<?php

namespace Hanzo\Bundle\AccountBundle\Handler;

use Hanzo\Core\Tools;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LogoutSuccessHandler
 *
 * @package Hanzo\Bundle\AccountBundle\Handler
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * setting up the service
     *
     * @param Container $container
     * @param Router    $router
     */
    public function __construct(Container $container, Router $router)
    {
        $this->container = $container;
        $this->router = $router;
    }


    /**
     * handle logout success response
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        switch ($this->container->get('kernel')->getSetting('store_mode')) {
            case 'webshop':
                $route = '_homepage';
                break;
            case 'consultant':
                $route = 'login';
                break;
        }

        Tools::setCookie('basket', '(0) '.Tools::moneyFormat(0.00), 0, false);

        $url = $this->router->generate($route, ['_locale' => $request->getLocale()]);
        $response = new RedirectResponse($url);

        return $response;
    }
}
