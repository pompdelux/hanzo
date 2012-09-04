<?php

namespace Hanzo\Bundle\AccountBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
	
	public function onLogoutSuccess(Request $request)
	{
		// redirect the user to where they were before the logout process begun.
		$referer_url = $request->headers->get('referer');

		$response = new RedirectResponse($referer_url);
		return $response;
	}

}