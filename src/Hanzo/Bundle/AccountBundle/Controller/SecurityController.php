<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\WishlistsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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
                    // yeah, force to https...
                    $target = str_replace('p:', 'ps:', $request->query->get('target'));
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

    /**
     * Performs api login.
     *
     * Payload must be json and follow this pattern:
     *  {"username": "", "password": ""}
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function apiLoginAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = CustomersQuery::create()
            ->filterByEmail($data['username'])
            ->filterByPassword(sha1($data['password']))
            ->findOne();

        if (!$user instanceof Customers) {
            return $this->json_response([
                'status' => false,
            ], 403);
        }

        $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
        $this->container->get('security.context')->setToken($token);

        $list = WishlistsQuery::create()
            ->filterByCustomersId($user->getId())
            ->findOne();

        return $this->json_response([
            'status'  => true,
            'list_id' => $list ? $list->getId() : null,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function isAuthendicatedAction()
    {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json_response(['status' => true]);
        }

        return $this->json_response(['status' => false]);
    }
}
