<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class SecurityController
 *
 * @package Hanzo\Bundle\AccountBundle
 */
class SecurityController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        switch (strrchr($request->headers->get('referer'), '/')) {
            case '/basket':
                $target = $this->container->get('router')->generate('_checkout', [], true);
                break;

            default:
                if ($request->query->has('target')) {
                    $target = $request->query->get('target');
                } else {
                    if ('consultant' == $this->get('kernel')->getStoreMode()) {
                        $target = $this->container->get('router')->generate('_homepage', [], true);
                    } else {
                        $target = $this->container->get('router')->generate('_account', [], true);
                    }
                }
                break;
        }

        return $this->render('AccountBundle:Security:login.html.twig', [
            'page_type'     => 'login',
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
            'target'        => $target,
        ]);
    }
}
