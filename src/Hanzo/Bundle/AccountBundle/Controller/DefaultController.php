<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Hanzo\Core\CoreController,
    Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\Customers,
    Hanzo\Model\CustomersPeer,
    Hanzo\Model\Addresses;

use Hanzo\Bundle\AccountBundle\Security\User\ProxyUser,
    Hanzo\Bundle\AccountBundle\Form\Type\CustomersType;

class DefaultController extends CoreController
{
    public function indexAction()
    {

        // $x = $this->get('mail_manager');
        // $x->setMessage('account.create', array(
        //     'name' => 'anders and',
        //     'username' => 'aa',
        //     'password' => 'qwer123',
        // ));
        // $x->setTo('ulrik@o3.dk', 'ulrik nielsen');
        // $x->setFrom('ulrik@bellcom.dk', 'anders and');
        // $x->send();

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

                $name = trim($customer->getFirstName() . ' ' . $customer->getLastName());

                $mailer = $this->get('mail_manager');
                $mailer->setMessage('account.create', array(
                    'name' => 'anders and',
                    'username' => $customer->getEmail(),
                    'password' => $customer->getPasswordClear(),
                ));

                $mailer->setTo($customer->getEmail(), $name);
                $mailer->send();

                return $this->redirect($this->generateUrl('_account'));
            }
        }

        return $this->render('AccountBundle:Default:create.html.twig', array(
            'page_type' => 'create-account',
            'form' => $form->createView(),
            'domain_key' => $hanzo->get('core.domain_key')
        ));
    }

    /**
     * handle password retrival
     *
     * @return Responce object
     */
    public function passwordForgottenAction()
    {
        $message = '';
        $request = $this->getRequest();

        if ('POST' === $request->getMethod()) {
            // find the user by email address
            $customer = CustomersPeer::getByEmail($request->get('email'));
            if ($customer instanceof Customers) {
                $name = trim($customer->getFirstName() . ' ' . $customer->getLastName());

                $mailer = $this->get('mail_manager');
                $mailer->setMessage('password.forgotten', array(
                    'name' => $name,
                    'username' => $customer->getEmail(),
                    'password' => $customer->getPasswordClear(),
                ));

                $mailer->setTo($customer->getEmail(), $name);
                $mailer->send();
                $message = 'password.forgotten.resend';
            }
            else {
                $message = 'password.forgotten.not_found';
            }
        }

        return $this->render('AccountBundle:Default:password_forgotten.html.twig', array(
            'page_type' => 'password-forgotten',
            'message' => $message
        ));
    }
}
