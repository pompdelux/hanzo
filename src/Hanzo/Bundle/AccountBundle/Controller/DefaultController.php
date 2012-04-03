<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Addresses;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\OrdersPeer;

use Hanzo\Bundle\AccountBundle\Security\User\ProxyUser;
use Hanzo\Bundle\AccountBundle\Form\Type\CustomersType;
use Hanzo\Bundle\AccountBundle\Form\Type\AddressesType;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();

        if ($session->has('in_edit') && $this->getRequest()->get('stop')) {
            $this->get('event_dispatcher')->dispatch('order.edit.cancel', new FilterOrderEvent(OrdersPeer::getCurrent()));
        }

        return $this->render('AccountBundle:Default:index.html.twig', array(
            'page_type' => 'account',
        ));
    }

    public function createAction()
    {
        $hanzo = Hanzo::getInstance();
        $request = $this->getRequest();

        $domainKey = $hanzo->get('core.domain_key');

        $customer = new Customers();
        $addresses = new Addresses();

        $country = $this->getCountryOrCountries();

        if ( $country instanceOf Countries ) // else it is probably a list (PropelObjectCollection)
        {
            $addresses->setCountry( $country->getLocalName() );
            $addresses->setCountriesId( $country->getId() );
        }

        $customer->addAddresses($addresses);

        $form = $this->createForm(new CustomersType( true, new AddressesType( $country )), $customer);

        if ('POST' === $request->getMethod())
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $customer->setPasswordClear($customer->getPassword());
                $customer->setPassword(sha1($customer->getPassword()));

                $addresses->setFirstName( $customer->getFirstName() );
                $addresses->setLastName( $customer->getLastName() );

                $formData = $request->request->get('customers');

                if ( isset( $formData['newsletter'] )  && $formData['newsletter'] )
                {
                    $api = $this->get('newsletterapi');
                    $response = $api->subscribe($customer->getEmail(), $api->getListIdAvaliableForDomain());
                    if ( is_object($response) && $response->is_error )
                    {
                        // TODO: do something?
                    }
                }

                if ( !isset($formData['addresses'][0]['countries_id']) && isset($formData['addresses'][0]['country']) )
                {
                    // TODO: can the first instance of addresses be used? is it pass by ref when addAddreses is called?
                    $addresses = $customer->getAddresses();
                    $address = $addresses[0];
                    $country = CountriesPeer::retrieveByPK($formData['addresses'][0]['country']);
                    $address->setCountriesId( $country->getId() );
                    $address->setCountry( $country->getLocalName() );
                    // TODO: delete existing??
                    $customer->addAddresses($address);
                }

                $customer->save();

                // login user
                $user = new ProxyUser($customer);
                $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
                $this->container->get('security.context')->setToken($token);

                $this->get('session')->setFlash('notice', 'account.created');

                // TODO: should all fields not have been trimmed?
                $name = trim($customer->getFirstName() . ' ' . $customer->getLastName());

                try
                {
                    $mailer = $this->get('mail_manager');
                    $mailer->setMessage('account.create', array(
                        'name' => $name,
                        'username' => $customer->getEmail(),
                        'password' => $customer->getPasswordClear(),
                    ));

                    $mailer->setTo($customer->getEmail(), $name);
                    $mailer->send();
                }
                catch (\Swift_TransportException $e)
                {
                    error_log(__LINE__.':'.__FILE__.' '.print_r($e->getMessage(),1)); // hf@bellcom.dk debugging
                }

                return $this->redirect($this->generateUrl('_account'));
            }
        }

        return $this->render('AccountBundle:Default:create.html.twig', array(
            'page_type' => 'create-account',
            'form' => $form->createView(),
            'domain_key' => $domainKey
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
                try{
                    $mailer->send();
                }
                catch (Exception $e) {}
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

    /**
     * Handles the user edit form.
     *
     * @return Response object
     */
    public function editAction()
    {
        $customer = CustomersPeer::getCurrent();
        $country = $this->getCountryOrCountries();

        $form = $this->createForm(new CustomersType(FALSE, new AddressesType( $country )), $customer);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                if (!$customer->getPassword()) {
                    $customer->setPassword(sha1($customer->getPasswordClear()));
                }
                $customer->save();

                $this->get('session')->setFlash('notice', 'account.updated');
                return $this->redirect($this->generateUrl('_account'));
            }
        }

        return $this->render('AccountBundle:Default:edit.html.twig', array(
            'page_type' => 'create-account',
            'form' => $form->createView(),
        ));
    }

    /**
     * getCountryOrCountries
     * @return mixed
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function getCountryOrCountries()
    {
        $hanzo = Hanzo::getInstance();
        $domainKey = $hanzo->get('core.domain_key');

        switch ($domainKey)
        {
            case 'DK':
                $country = CountriesPeer::retrieveByPK(58);
                break;
            case 'COM':
                $country = CountriesQuery::create()->find(); // Note that .com returns all countries
                break;
            case 'SE':
                $country = CountriesPeer::retrieveByPK(207);
                break;
            case 'NO':
                $country = CountriesPeer::retrieveByPK(161);
                break;
            case 'NL':
                $country = CountriesPeer::retrieveByPK(151);
                break;
        }

        return $country;
    }
}
