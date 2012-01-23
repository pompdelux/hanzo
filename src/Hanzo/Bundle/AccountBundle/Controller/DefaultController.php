<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Hanzo\Core\CoreController,
    Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\Addresses;

use Hanzo\Bundle\AccountBundle\Security\User\ProxyUser;
use Hanzo\Bundle\AccountBundle\Form\Type\CustomersType;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('AccountBundle:Default:index.html.twig', array(
            'page_type' => 'account'
        ));
    }

    //
    public function createAction()
    {
        $hanzo = Hanzo::getInstance();

        $addresses = new Addresses();
        $addresses->setCountry('Danmark');

        $customer = new Customers();
        $customer->addAddresses($addresses);

        $form = $this->createForm(new CustomersType(), $customer);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $customer->setLanguagesId($hanzo->get('core.language_id'));
                $customer->setPasswordClear($customer->getPassword());
                $customer->setPassword(sha1($customer->getPassword()));
                $customer->save();

                // login user
                $user = new ProxyUser($customer);
                $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
                $this->container->get('security.context')->setToken($token);

                $this->get('session')->setFlash('notice', 'account.created');

                return $this->redirect($this->generateUrl('_account'));
            }
        }

        return $this->render('AccountBundle:Default:create.html.twig', array(
            'page_type' => 'create-account',
            'form' => $form->createView(),
            'domain_key' => $hanzo->get('core.domain_key')
        ));
    }

    public function passwordForgottenAction(){}
}
