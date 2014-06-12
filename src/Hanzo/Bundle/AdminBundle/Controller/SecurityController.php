<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends CoreController
{
    public function loginAction(Request $request)
    {
        if ('POST' === $request->getMethod()) {
            $customer = CustomersQuery::create()
                ->filterByEmail($request->request->get('_username'))
                ->filterByPassword(sha1($request->request->get('_password')))
                ->findOne()
            ;

            if ($customer instanceof Customers) {
                $roles = $customer->getRoles();
                if (($customer->getGroupsId() < 3) && !in_array('ROLE_EMPLOYEE', $roles)) {
                    return $this->render('AdminBundle:Security:login.html.twig');
                }

                $token = new UsernamePasswordToken($customer, null, 'secured_area', $roles);
                $this->container->get('security.context')->setToken($token);

                return $this->redirect($this->generateUrl('admin'));
            }
        }

        return $this->render('AdminBundle:Security:login.html.twig');
    }
}
