<?php /* vim: set sw=4: */
namespace Hanzo\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\FormErrors;

use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
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

        $countries = CountriesPeer::getAvailableDomainCountries();

        if ( count( $countries ) == 1 ) // for .dk, .se, .no and maybe .nl
        {
            $addresses->setCountry( $countries[0]->getLocalName() );
            $addresses->setCountriesId( $countries[0]->getId() );

            error_log(__LINE__.':'.__FILE__.' '.print_r($addresses,1)); // hf@bellcom.dk debugging
        }
        else
        {
            $geoip = $this->get('geoip_manager');
            $geoipResult = $geoip->lookup();
            if ( !is_null( $geoipResult['country_id'] ) )
            {
                error_log(__LINE__.':'.__FILE__.' '.print_r($geoipResult,1)); // hf@bellcom.dk debugging
                $addresses->setCountry( $geoipResult['country_localname'] );
                $addresses->setCountriesId( $geoipResult['country_id'] );

                error_log(__LINE__.':'.__FILE__.' '.print_r($addresses,1)); // hf@bellcom.dk debugging
            }
        }

        $customer->addAddresses($addresses);

        $errors = '';
        $form = $this->createForm(
            new CustomersType(true, new AddressesType($countries)),
            $customer,
            array('validation_groups' => 'customer')
        );

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
                        $this->get('session')->setFlash('warning', 'account.newsletter.warning');
                    }
                }

                $customer->save();

                // login user
                $user = new ProxyUser($customer);
                $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());

                $this->container->get('security.context')->setToken($token);
                $this->get('session')->setFlash('notice', 'account.created');

                $name = $customer->getFirstName() . ' ' . $customer->getLastName();

                try {
                    $mailer = $this->get('mail_manager');
                    $mailer->setMessage('account.create', array(
                        'name'     => $name,
                        'username' => $customer->getEmail(),
                        'password' => $customer->getPasswordClear(),
                    ));

                    $mailer->setTo($customer->getEmail(), $name);
                    $mailer->send();
                } catch (\Swift_TransportException $e) {
                    error_log(__LINE__.':'.__FILE__.' '.print_r($e->getMessage(),1)); // hf@bellcom.dk debugging
                }

                $order = OrdersPeer::getCurrent();

                if ($order->isNew()) {
                    return $this->redirect($this->generateUrl('_account'));
                }

                // if needed, recalculate the order.
                if ($order->getTotalPrice(true)) {
                    $order->recalculate();
                    $order->save();
                }

                return $this->redirect($this->generateUrl('_checkout'));

            } else {
                $errors = new FormErrors($form, $this->get('translator'), 'account');
                $errors = $errors->toString();
            }
        }

        return $this->render('AccountBundle:Default:create.html.twig', array(
            'page_type' => 'create-account',
            'form' => $form->createView(),
            'errors' => $errors,
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
        $countries = CountriesPeer::getAvailableDomainCountries();

        $form = $this->createForm(new CustomersType(false, new AddressesType( $countries )), $customer);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                if (!$customer->getPassword()) {
                    $customer->setPassword(sha1($customer->getPasswordClear()));
                }
                else
                {
                    $customer->setPasswordClear($customer->getPassword());
                    $customer->setPassword(sha1($customer->getPassword()));
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


    public function editAddressAction($type = 'payment', $shipping_id = null)
    {
        $request = $this->getRequest();
        $translator = $this->get('translator');

        // hack...
        if (isset($_POST)) {
            if (isset($_POST['form']['type']) && $type != $_POST['form']['type']) {
                $type = $_POST['form']['type'];
            }
            if (isset($_POST['form']['company_name'])) {
                $shipping_id = 11;
            }
        }


        $customer = CustomersPeer::getCurrent();
        $address = AddressesQuery::create()
            ->filterByType($type)
            ->findOneByCustomersId($customer->getId())
        ;

        if (!$address instanceof Addresses) {
            $address = new Addresses();
            $address->setCustomersId($customer->getId());
            $address->setType($type);
            $address->setFirstName($customer->getFirstName());
            $address->setLastName($customer->getLastName());
        }

        $validation_group = $type;
        if ($shipping_id == 11) {
            $validation_group = 'company_' . $type;
        }
        else {
          $address->setCompanyName(null);
        }

        $builder = $this->createFormBuilder($address, array(
            'validation_groups' => $validation_group
        ));

        $builder->add('type', 'hidden', array('data' => $type));

        // NICETO: fix the "11" (company spipping) hack
        if (($type == 'shipping') && ($shipping_id == 11)) {
            $builder->add('company_name', null, array(
                'label' => 'company.name',
                'required' => true,
                'translation_domain' => 'account'
            ));
        }

        $builder->add('first_name', null, array('required' => true, 'translation_domain' => 'account'));
        $builder->add('last_name', null, array('required' => true, 'translation_domain' => 'account'));
        $builder->add('postal_code', null, array('required' => true, 'translation_domain' => 'account'));
        $builder->add('city', null, array('required' => true, 'translation_domain' => 'account'));

        $countries = CountriesPeer::getAvailableDomainCountries(true);

        if ('overnightbox' !== $type) {
            $builder->add('address_line_1', null, array('required' => true, 'translation_domain' => 'account'));

            if (count($countries) > 1) {
                $builder->add('countries_id', 'choice', array(
                    'empty_value' => 'choose.country',
                    'choices' => $countries,
                    'required' => true,
                    'translation_domain' => 'account'
                ));
            } else {
                list($country_id, $country_name) = each($countries);

                $address->setCountriesId($country_id);
                $address->setCountry($country_name);

                $builder->add('countries_id', 'hidden', array('data' => $country_id));
                $builder->add('country', null, array(
                    'read_only' => true,
                    'translation_domain' => 'account'
                ));
            }
        } else {
            list($country_id, $country_name) = each($countries);
            $address->setCountriesId($country_id);
            $address->setCountry($country_name);

            $builder->add('countries_id', 'hidden', array('data' => $country_id));
            $builder->add('address_line_1', null, array(
                'label' => 'overnightbox.label',
                'required' => true,
                'translation_domain' => 'account'
            ));
        }

        $form = $builder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $address->save();

                // NICETO, implement elsewhere
                if ($type == 'shipping') {
                    $order = OrdersPeer::getCurrent();
                    if (false == $order->isNew()) {
                        $order->setDeliveryFirstName($address->getFirstName());
                        $order->setDeliveryLastName($address->getLastName());
                        $order->setDeliveryAddressLine1($address->getAddressLine1());
                        $order->setDeliveryAddressLine2($address->getAddressLine2());
                        $order->setDeliveryPostalCode($address->getPostalCode());
                        $order->setDeliveryCity($address->getCity());
                        $order->setDeliveryCountry($address->getCountry());
                        $order->setDeliveryCountriesId($address->getCountriesId());
                        $order->setDeliveryStateProvince($address->getStateProvince());
                        $order->setDeliveryCompanyName($address->getCompanyName());
                        $order->setDeliveryMethod($shipping_id);
                        $order->save();
                    }
                }

                if ('json' === $this->getFormat()) {
                    return $this->json_response(array(
                        'status' => true,
                        'message' => '',
                        'data' => array(
                            'address' => $address->toArray(\BasePeer::TYPE_FIELDNAME)
                        ),
                    ));
                }
            } else {
                if ('json' === $this->getFormat()) {
                    $errors = array();
                    foreach ($form->getChildren() as $id => $element) {
                        if ($element->hasErrors()) {
                            foreach ($element->getErrors() as $error) {
                                $errors[$id][] = $translator->trans($error->getMessageTemplate(), array(), 'account');
                            }
                        }
                     }

                    if (count($errors)) {
                        return $this->json_response(array(
                            'status' => false,
                            'message' => $translator->trans('create.account.error', array(), 'account'),
                            'data' => $errors,
                        ));
                    }
                }
            }
        }

        $html = $this->render('AccountBundle:Default:address_form_block.html.twig', array(
            'page_type' => 'edit-address-block',
            'title' => ($address->isNew() ? 'add.address' : 'edit.address'),
            'form' => $form->createView(),
            'callback' => 'postal'
        ));

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => true,
                'message' => '',
                'data' => array('html' => $html->getContent()),
            ));
        }

        return $html;
    }


    /**
     * used to validate via ajax
     *
     * @param  string $type
     * @return Response
     */
    public function checkAction($type)
    {
        $status = true;
        $message = '';
        $data = array();

        if ('email' == $type) {
            $translator = $this->get('translator');
            $account = CustomersQuery::create()->findOneByEmail($this->getRequest()->get('email'));
            if ($account instanceof Customers) {
                $status = false;
                $message = $translator->trans('email.already.in.use', array(), 'account');
                $data = array('title' => $translator->trans('create.account.error.title', array(), 'account'));
            }
        }


        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ));
        }
    }
}
