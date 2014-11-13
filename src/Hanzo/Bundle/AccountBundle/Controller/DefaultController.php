<?php /* vim: set sw=4: */
namespace Hanzo\Bundle\AccountBundle\Controller;

use Hanzo\Bundle\AccountBundle\Form\Type\CustomersType;
use Hanzo\Bundle\AccountBundle\Form\Type\AddressesType;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\FormErrors;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\AccountBundle
 */
class DefaultController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // if we access the account page with an active "edit" we close it.
        if ($request->getSession()->has('in_edit')) {
            $this->get('event_dispatcher')->dispatch('order.edit.cancel', new FilterOrderEvent(OrdersPeer::getCurrent()));

            // update/set basket cookie
            Tools::setCookie('basket', '(0) '.Tools::moneyFormat(0.00), 0, false);

            return $this->redirect($this->generateUrl('_account'));
        }

        // some times edit order cookies are not "closed"
        if ($request->cookies->has('__ice')) {
            $order = OrdersPeer::getCurrent();

            if ($order->isNew()) {
                Tools::unsetEditCookies();
            }
        }

        return $this->render('AccountBundle:Default:index.html.twig', [
            'page_type' => 'account',
            'user'      => CustomersPeer::getCurrent(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function createAction(Request $request)
    {
        $hanzo      = Hanzo::getInstance();
        $translator = $this->get('translator');
        $domainKey  = $hanzo->get('core.domain_key');

        $customer  = new Customers();
        $addresses = new Addresses();

        $countries = CountriesPeer::getAvailableDomainCountries();

        // for .dk, .se, .no and maybe .nl
        if (count($countries) == 1) {
            $addresses->setCountry($countries[0]->getLocalName());
            $addresses->setCountriesId($countries[0]->getId());
        } else {
            // If the customer is located in Denmark, but browsing the en_GB site, the geoip will set country to Denmark,
            // and fail to select a country id in the dropdown as Denmark is excluded from that list
            // But when the customer submits the form "Denmark" is still send as a country so we try to override that here

            if ('POST' !== $request->getMethod()) {
                $geoip = $this->get('muneris.maxmind');
                $geoipResult = $geoip->lookup();

                if (!empty($geoipResult->city->country->code)) {
                    $c = CountriesQuery::create()
                        ->filterByIso2($geoipResult->city->country->code)
                        ->findOne();

                    $addresses->setCountry($c->getLocalName());
                    $addresses->setCountriesId($c->getId());
                }
            }
        }

        $customer->addAddresses($addresses);

        $errors = '';
        $form = $this->createForm(
            new CustomersType(true, new AddressesType($countries)),
            $customer,
            ['validation_groups' => 'customer']
        );

        if ( 'POST' === $request->getMethod() ) {
            $form->handleRequest($request);
            $data = $form->getData();
            // extra phone and zipcode constrints for .fi
            // TODO: figure out how to make this part of the validation process.
            if ('FI' == substr($domainKey, -2)) {
                // zip codes are always 5 digits in finland.
                if (!preg_match('/^[0-9]{5}$/', $customer->getAddresses()->toArray()[0]['PostalCode'])) {
                    $form->addError(new FormError('postal_code.required'));
                }

                // phonenumber must start with a 0 (zero)
                if (!preg_match('/^0[0-9]+$/', $customer->getPhone())) {
                    $form->addError(new FormError('phone.required'));
                }
            }

            if ($form->isValid()) {
                $customer->setPasswordClear($customer->getPassword());
                $customer->setPassword(sha1($customer->getPassword()));

                $addresses->setTitle($customer->getTitle());
                $addresses->setFirstName($customer->getFirstName());
                $addresses->setLastName($customer->getLastName());

                $formData = $request->request->get('customers');

                // for .dk, .se, .no and maybe .nl
                if (1 != count($countries)) {
                    $countryById = CountriesQuery::create()->findPk($formData['addresses'][0]['countries_id']);
                    $addresses->setCountry($countryById->getName());
                }

                if (isset($formData['newsletter'])  && $formData['newsletter']) {
                    $api = $this->get('newsletterapi');
                    $response = $api->subscribe($customer->getEmail(), $api->getListIdAvaliableForDomain());
                    if (is_object($response) && $response->is_error) {
                        $this->get('session')->getFlashBag()->add('warning', 'account.newsletter.warning');
                    }
                }

                $customer->save();
                $token = new UsernamePasswordToken($customer, null, 'secured_area', $customer->getRoles());

                $this->container->get('security.context')->setToken($token);
                $this->get('session')->getFlashBag()->add('notice', $translator->trans('account.created', [], 'account'));

                $name = $customer->getFirstName() . ' ' . $customer->getLastName();

                try {
                    $mailer = $this->get('mail_manager');
                    $mailer->setMessage('account.create', [
                        'name'     => $name,
                        'username' => $customer->getEmail(),
                        'password' => $customer->getPasswordClear(),
                    ]);

                    $mailer->setTo($customer->getEmail(), $name);
                    $mailer->send();
                } catch (\Swift_TransportException $e) {
                    Tools::log($e->getMessage());
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

        return $this->render('AccountBundle:Default:create.html.twig', [
            'page_type'  => 'create-account',
            'form'       => $form->createView(),
            'errors'     => $errors,
            'domain_key' => $domainKey
        ]);
    }

    /**
     * handle password retrival
     *
     * @param Request  $request
     *
     * @return Response object
     */
    public function passwordForgottenAction(Request $request)
    {
        $message = '';

        if ('POST' === $request->getMethod()) {

            if (false === filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL)) {
                return $this->render('AccountBundle:Default:password_forgotten.html.twig', [
                    'page_type' => 'password-forgotten',
                    'message'   => 'password.forgotten.not_found'
                ]);
            }

            // find the user by email address
            $customer = CustomersPeer::getByEmail($request->request->get('email'));
            if ($customer instanceof Customers) {
                $name = trim($customer->getFirstName() . ' ' . $customer->getLastName());

                $mailer = $this->get('mail_manager');
                $mailer->setMessage('password.forgotten', [
                    'name'     => $name,
                    'username' => $customer->getEmail(),
                    'password' => $customer->getPasswordClear(),
                ]);

                $mailer->setTo($customer->getEmail(), $name);
                try {
                    $mailer->send();
                } catch (\Exception $e) {
                }

                $message = 'password.forgotten.resend';
            } else {
                $message = 'password.forgotten.not_found';
            }
        }

        return $this->render('AccountBundle:Default:password_forgotten.html.twig', [
            'page_type' => 'password-forgotten',
            'message'   => $message
        ]);
    }

    /**
     * Handles the user edit form.
     *
     * @param Request $request
     *
     * @return Response object
     */
    public function editAction(Request $request)
    {
        $customer = CustomersPeer::getCurrent();

        // you cannot edit a new customer.
        if ($customer->isNew()) {
            return $this->redirect($this->generateUrl('_account'));
        }

        $countries = CountriesPeer::getAvailableDomainCountries();

        $errors = '';
        $form = $this->createForm(
            new CustomersType(false, new AddressesType($countries)),
            $customer,
            ['validation_groups' => 'customer_edit']
        );

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if (!$customer->getPassword()) {
                    $customer->setPassword(sha1($customer->getPasswordClear()));
                } else {
                    $customer->setPasswordClear($customer->getPassword());
                    $customer->setPassword(sha1($customer->getPassword()));
                }
                $customer->save();

                $this->get('session')->getFlashBag()->add('notice', 'account.updated');

                return $this->redirect($this->generateUrl('_account'));
            } else {
                $errors = new FormErrors($form, $this->get('translator'), 'account');
                $errors = $errors->toString();
            }

        }

        return $this->render('AccountBundle:Default:edit.html.twig', [
            'page_type' => 'edit-account',
            'errors'    => $errors,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * @param Request  $request
     * @param string   $type
     * @param int|null $shipping_id
     *
     * @return Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function editAddressAction(Request $request, $type = 'payment', $shipping_id = null)
    {
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

        // hf@bellcom.dk, 20-aug-2012: when a consultant places an order, this has to get the customer from the order -->>
        $order = OrdersPeer::getCurrent();

        if ($order->getCustomersId()) {
            $customer = $order->getCustomers();
        } else {
            $customer = CustomersPeer::getCurrent();
        }
        // <<-- hf@bellcom.dk, 20-aug-2012: when a consultant places an order, this has to get the customer from the order

        $address = AddressesQuery::create()
            ->filterByType($type)
            ->findOneByCustomersId($customer->getId());

        if (!$address instanceof Addresses) {
            $address = new Addresses();
            $address->setCustomersId($customer->getId());
            $address->setType($type);
            $address->setFirstName($customer->getFirstName());
            $address->setLastName($customer->getLastName());
        }

        $validationGroup = $type;
        if ($shipping_id == 11) {
            $validationGroup = 'company_' . $type;
        } else {
          $address->setCompanyName(null);
        }

        $builder = $this->createFormBuilder($address, [
            'validation_groups' => $validationGroup
        ]);

        $builder->add('type', 'hidden', ['data' => $type]);

        // NICETO: fix the "11" (company spipping) hack
        if (($type == 'shipping') && ($shipping_id == 11)) {
            $builder->add('company_name', null, [
                'label'              => 'company.name',
                'required'           => true,
                'translation_domain' => 'account'
            ]);
        }

        $builder->add('first_name', null, ['required' => true, 'translation_domain' => 'account']);
        $builder->add('last_name', null, ['required' => true, 'translation_domain' => 'account']);
        $builder->add('postal_code', null, ['required' => true, 'translation_domain' => 'account']);
        $builder->add('city', null, ['required' => true, 'translation_domain' => 'account']);

        $countries = CountriesPeer::getAvailableDomainCountries(true);

        if ('overnightbox' !== $type) {
            $builder->add('address_line_1', null, ['required' => true, 'translation_domain' => 'account']);

            if (count($countries) > 1) {
                $builder->add('countries_id', 'choice', [
                    'empty_value'        => 'choose.country',
                    'choices'            => $countries,
                    'required'           => true,
                    'translation_domain' => 'account'
                ]);
            } else {
                list($countryId, $countryName) = each($countries);

                $address->setCountriesId($countryId);
                $address->setCountry($countryName);

                $builder->add('countries_id', 'hidden', ['data' => $countryId]);
                $builder->add('country', null, [
                    'read_only'          => true,
                    'translation_domain' => 'account'
                ]);
            }
        } else {
            list($countryId, $countryName) = each($countries);
            $address->setCountriesId($countryId);
            $address->setCountry($countryName);

            $builder->add('countries_id', 'hidden', ['data' => $countryId]);
            $builder->add('address_line_1', null, [
                'label'              => 'overnightbox.label',
                'required'           => true,
                'translation_domain' => 'account'
            ]);
        }

        $form = $builder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $address->save();
                // NICETO, implement elsewhere
                if ($type == 'shipping') {

                    if (!$order->isNew()) {
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
                    return $this->json_response([
                        'status'  => true,
                        'message' => '',
                        'data'    => [
                            'address' => $address->toArray(\BasePeer::TYPE_FIELDNAME)
                        ],
                    ]);
                }
            } else {
                if ('json' === $this->getFormat()) {
                    $errors = [];
                    foreach ($form->getChildren() as $id => $element) {
                        if ($element->hasErrors()) {
                            foreach ($element->getErrors() as $error) {
                                $errors[$id][] = $translator->trans($error->getMessageTemplate(), [], 'account');
                            }
                        }
                    }

                    if (count($errors)) {
                        return $this->json_response([
                            'status'  => false,
                            'message' => $translator->trans('create.account.error', [], 'account'),
                            'data'    => $errors,
                        ]);
                    }
                }
            }
        }

        $html = $this->render('AccountBundle:Default:address_form_block.html.twig', [
            'page_type' => 'edit-address-block',
            'title'     => ($address->isNew() ? 'add.address' : 'edit.address'),
            'form'      => $form->createView(),
            'callback'  => 'postal'
        ]);

        if ('json' === $this->getFormat()) {
            return $this->json_response([
                'status'  => true,
                'message' => '',
                'data'    => ['html' => $html->getContent()],
            ]);
        }

        return $html;
    }

    /**
     * used to validate via ajax
     *
     * @param string $type
     *
     * @return Response
     */
    public function checkAction($type)
    {
        $status = true;
        $message = '';
        $data = [];

        if ('email' == $type) {
            $customer = CustomersPeer::getCurrent();

            $translator = $this->get('translator');
            $account = CustomersQuery::create()
                ->filterById($customer->getId(), \Criteria::NOT_EQUAL)
                ->findOneByEmail($this->getRequest()->get('email'));

            if ($account instanceof Customers) {
                $status  = false;
                $message = $translator->trans('email.already.in.use', [], 'account');
                $data    = ['title' => $translator->trans('create.account.error.title', [], 'account')];
            }
        }

        if ('json' === $this->getFormat()) {
            return $this->json_response([
                'status'  => $status,
                'message' => $message,
                'data'    => $data,
            ]);
        }
    }

    /**
     * errorAction
     *
     * @param string $error
     *
     * @return Response
     */
    public function errorAction($error)
    {
        return $this->render('AccountBundle:Default:error.html.twig', []);
    }

    /**
     * @param int $order_id
     *
     * @return Response
     */
    public function stopOrderEditAction($order_id)
    {
        if ($order_id) {
            $this->get('event_dispatcher')->dispatch('order.edit.cancel', new FilterOrderEvent(OrdersQuery::create()->findPk($order_id)));
        }

        return $this->redirect($this->generateUrl('_account'));
    }
}
