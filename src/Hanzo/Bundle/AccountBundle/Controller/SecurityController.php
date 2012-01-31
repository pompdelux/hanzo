<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\Stock,
    Hanzo\Core\CoreController;

class SecurityController extends CoreController
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        switch (strrchr($request->headers->get('referer'), '/')) {
            case '/basket':
                $target = '_checkout';
                break;
            default:
                $target = '_account';
                break;
        }

        return $this->render('AccountBundle:Security:login.html.twig', array(
            'page_type' => 'login',
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
            'target' => $target
        ));
    }
}
