<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Criteria;
use Hanzo\Bundle\AccountBundle\Form\Type\CustomersType;
use Hanzo\Bundle\AccountBundle\Form\Type\AddressesType;
use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\OrdersPeer;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class DefaultController extends CoreController
{
    /**
     * createCustomerAction
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createCustomerAction(Request $request)
    {
        $order = OrdersPeer::getCurrent();

        // if the customer has been adding stuff to the basket, use that information here.
        $customerId = $request->request->get('id');
        $hanzo     = Hanzo::getInstance();
        $domainKey = $hanzo->get('core.domain_key');
        $errors    = '';
        $countries = CountriesPeer::getAvailableDomainCountries();

        // If order is for the hostess, find her and use the Customer
        $isHostess = $order->isHostessOrder();

        if ($isHostess === true) {
            $event = EventsQuery::create()
                ->filterById($order->getEventsId())
                ->findOne();

            if ($event->getCustomersId()) {
                $customerId = $event->getCustomersId();
            } else {
                $isHostess = false;
            }
        }

        if ('POST' == $request->getMethod() || $isHostess) {
            if ($customerId) {
                $customer = CustomersQuery::create()
                    ->joinWithAddresses()
                    ->useAddressesQuery()
                    ->filterByType('payment')
                    ->endUse()
                    ->findOneById($customerId);

                if ($customer instanceof Customers) {
                    $pwd               = $customer->getPassword();
                    $address           = $customer->getAddresses()->getFirst();
                    $validationGroups = 'customer_edit';
                }
            }
        }

        if (empty($address)) {
            $customer = new Customers();
            $address  = new Addresses();

            if (count($countries) == 1) {
                $address->setCountry($countries[0]->getLocalName());
                $address->setCountriesId($countries[0]->getId());
            }

            $customer->addAddresses($address);
            $validationGroups = 'customer';

        }

        $email = $customer->getEmail();

        $form = $this->createForm(
            new CustomersType(true, new AddressesType($countries)),
            $customer,
            ['validation_groups' => $validationGroups]
        );

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            $data = $form->getData();

            // verify that the email is not already in use.
            if (!$customer->isNew() && $email) {
                $formEmail = $data->getEmail();

                if ($email != $formEmail) {
                    $c = CustomersQuery::create()
                        ->filterById($customer->getId(), Criteria::NOT_EQUAL)
                        ->findOneByEmail($formEmail);

                    if ($c instanceof Customers) {
                        $form->addError(new FormError('email.exists'));
                    }
                }
            }

            // extra phone and zipcode constrints for .fi
            // TODO: figure out how to make this part of the validation process.
            if ('FI' == substr($domainKey, -2)) {
                // zip codes are always 5 digits in finland.
                if (!preg_match('/^[0-9]{5}$/', $address->getPostalCode())) {
                    $form->addError(new FormError('postal_code.required'));
                }

                // phonenumber must start with a 0 (zero)
                if (!preg_match('/^0[0-9]+$/', $customer->getPhone())) {
                    $form->addError(new FormError('phone.required'));
                }
            }

            if ($form->isValid()) {
                if (!$customer->getPassword()) {
                    $customer->setPassword($pwd);
                } elseif ($customer->isNew()) {
                    $pwd = $customer->getPassword();
                    $customer->setPassword(sha1($pwd));
                    $customer->setPasswordClear($pwd);
                }

                $address->setFirstName($customer->getFirstName());
                $address->setLastName($customer->getLastName());

                $customer->save();
                $address->save();

                $formData = $request->request->get('customers');

                if (isset($formData['newsletter']) && $formData['newsletter']) {
                    $api = $this->get('newsletterapi');
                    $api->subscribe($customer->getEmail(), $api->getListIdAvaliableForDomain());
                }

                $order->setCustomersId($customer->getId());
                $order->setFirstName($customer->getFirstName());
                $order->setLastName($customer->getLastName());
                $order->setEmail($customer->getEmail());
                $order->setPhone($customer->getPhone());

                $order->setBillingTitle($address->getTitle());
                $order->setBillingFirstName($address->getFirstName());
                $order->setBillingLastName($address->getLastName());
                $order->setBillingAddressLine1($address->getAddressLine1());
                $order->setBillingAddressLine2($address->getAddressLine2());
                $order->setBillingPostalCode($address->getPostalCode());
                $order->setBillingCity($address->getCity());
                $order->setBillingCountry($address->getCountry());
                $order->setBillingCountriesId($address->getCountriesId());
                $order->setBillingStateProvince($address->getStateProvince());
                $order->save();

                return $this->redirect($this->generateUrl('_checkout'));
            }
        }

        return $this->render('EventsBundle:Default:create_customer.html.twig', [
            'page_type'  => 'events-create-customer',
            'is_hostess' => $isHostess,
            'form'       => $form->createView(),
            'errors'     => $errors,
            'domain_key' => $domainKey,
        ]);
    }

    /**
     * fetchCustomerAction
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return Response
     */
    public function fetchCustomerAction(Request $request)
    {
        $value  = $request->request->get('value');
        $type   = strpos($value, '@') ? 'email' : 'phone';
        $api    = $this->get('newsletterapi');
        $listId = $api->getListIdAvaliableForDomain();
        $error  = true;
        $data   = [];

        switch ($type) {
            case 'email':
                $customer = CustomersQuery::create()
                    ->findOneByEmail($value);

                if ($customer instanceof Customers) {
                    $c = new Criteria();
                    $c->addAscendingOrderByColumn(
                        sprintf(
                            "FIELD(%s, '%s', '%s')",
                            AddressesPeer::TYPE,
                            'payment',
                            'shipping'
                        )
                    );

                    $c->add(AddressesPeer::TYPE, 'payment');
                    $c->setLimit(1);

                    $address = $customer->getAddressess($c);
                    $address = $address->getFirst();

                    if ($address instanceof Addresses) {
                        $data = [
                            'id'             => $customer->getId(),
                            'first_name'     => $customer->getFirstName(),
                            'last_name'      => $customer->getLastName(),
                            'phone'          => $customer->getPhone(),
                            'email_address'  => $customer->getEmail(),
                            'address_line_1' => $address->getAddressLine1(),
                            'postal_code'    => $address->getPostalCode(),
                            'city'           => $address->getCity(),
                            'countries_id'   => $address->getCountriesId(),
                            'country'        => $address->getCountry(),
                            'newsletter'     => $api->getSubscriptionStateByEmail($customer->getEmail(), $listId),
                        ];
                    }
                }
                break;

            case 'phone':
                $domainKey = Hanzo::getInstance()->get('core.domain_key');

                // phone number lookup only in denmark
                if (!in_array($domainKey, ['DK', 'SalesDK'])) {
                    break;
                }

                $result = $this->forward('MunerisBundle:Nno:lookup', ['number' => $value]);

                if (200 == $result->getStatusCode()) {
                    $result = json_decode($result->getContent());
                    if (isset($result->data)) {
                        $data = [
                            'first_name'     => '',
                            'last_name'      => '',
                            'phone'          => '',
                            'address_line_1' => '',
                            'postal_code'    => '',
                            'city'           => '',
                            'countries_id'   => 58,
                            'country'        => 'Denmark',
                        ];

                        $map = [
                            'first_name'  => 'christianname',
                            'last_name'   => 'surname',
                            'phone'       => 'phone',
                            'address'     => 'address_line_1',
                            'postal_code' => 'zipcode',
                            'city'        => 'district',
                        ];

                        foreach ($map as $key => $prop) {
                            if (isset($result->data->number->$prop)) {
                                $data[$key] = $result->data->number->$prop;
                            }
                        }
                    }
                }

                break;
        }

        return $this->json_response([
            'status'  => $error,
            'message' => '',
            'data'    => $data,
        ]);
    }
}
