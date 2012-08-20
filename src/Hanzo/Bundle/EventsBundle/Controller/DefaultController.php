<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Criteria;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\OrdersPeer;

use Hanzo\Bundle\AccountBundle\Form\Type\CustomersType;
use Hanzo\Bundle\AccountBundle\Form\Type\AddressesType;

use Hanzo\Bundle\AccountBundle\NNO\NNO;
use Hanzo\Bundle\AccountBundle\NNO\SearchQuestion;
use Hanzo\Bundle\AccountBundle\NNO\nnoSubscriber;
use Hanzo\Bundle\AccountBundle\NNO\nnoSubscriberResult;

class DefaultController extends CoreController
{

    public function indexAction($name)
    {
        return $this->render('EventsBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * createCustomerAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function createCustomerAction()
    {
        $request = $this->getRequest();
        $consultant = CustomersPeer::getCurrent();
        $order = OrdersPeer::getCurrent();

        // if the customer has been adding stuff to the basket, use that information here.
        $customer_id = $request->get('id');
        if ($consultant->getId() != $order->getCustomersId()) {
            $customer_id = $order->getCustomersId();
        }

        $hanzo = Hanzo::getInstance();
        $domainKey = $hanzo->get('core.domain_key');
        $errors = '';

        $countries = CountriesPeer::getAvailableDomainCountries();

        if ('POST' == $request->getMethod()) {
            if ($customer_id) {
                $customer = CustomersQuery::create()
                    ->joinWithAddresses()
                    ->useAddressesQuery()
                        ->filterByType('payment')
                    ->endUse()
                    ->findOneById($customer_id)
                ;
                $pwd = $customer->getPassword();
                $address = $customer->getAddresses()->getFirst();
                $validation_groups = 'customer_edit';
            }
        }

        if (empty($address)) {
            $customer = new Customers();
            $address = new Addresses();
            if ( count( $countries ) == 1 ) {
                $address->setCountry( $countries[0]->getLocalName() );
                $address->setCountriesId( $countries[0]->getId() );
            }

            $customer->addAddresses($address);
            $validation_groups = 'customer';
        }

        $form = $this->createForm(new CustomersType(true, new AddressesType($countries)), $customer, array('validation_groups' => $validation_groups));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                if (!$customer->getPassword()) {
                    $customer->setPassword($pwd);
                } elseif ($customer->isNew()) {
                    $customer->setPassword(sha1($customer->getPassword()));
                    $customer->setPasswordClear($customer->getPassword());
                }

                $address->setFirstName( $customer->getFirstName() );
                $address->setLastName( $customer->getLastName() );

                $customer->save();
                $address->save();

                $order->setCustomersId($customer->getId());
                $order->setFirstName($customer->getFirstName());
                $order->setLastName($customer->getLastName());
                $order->setEmail($customer->getEmail());
                $order->setPhone($customer->getPhone());

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

        return $this->render('EventsBundle:Default:create_customer.html.twig', array(
            'page_type' => 'events-create-customer',
            'form' => $form->createView(),
            'errors' => $errors,
            'domain_key' => $domainKey
            ));
    }

    /**
     * fetchCustomerAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function fetchCustomerAction()
    {
        $request = $this->getRequest();
        $value = $request->get('value');
        $type = strpos($value, '@') ? 'email' : 'phone';

        $error = true;
        $data = array();

        switch ($type) {
          case 'email':
              $customer = CustomersQuery::create()
                  ->findOneByEmail($value);

                if ($customer instanceof Customers) {
                    $c = new Criteria();
                    $c->add(AddressesPeer::TYPE, 'payment');
                    $address = $customer->getAddressess($c);
                    $address = $address->getFirst();

                    if ($address instanceof Addresses) {
                        $data = array(
                            'id' => $customer->getId(),
                            'first_name' => $customer->getFirstName(),
                            'last_name'  => $customer->getLastName(),
                            'phone' => $customer->getPhone(),
                            'email_address' => $customer->getEmail(),
                            'address_line_1' => $address->getAddressLine1(),
                            'postal_code' => $address->getPostalCode(),
                            'city' => $address->getCity(),
                            'countries_id' => $address->getCountriesId(),
                            'country' => $address->getCountry(),
                        );
                    }
                }
                break;

            case 'phone':
                $lookup = new SearchQuestion();
                $lookup->phone = $value;
                $lookup->username = 'delux';

                $nno = new NNO();
                $result = $nno->lookupSubscribers($lookup);

                if (($result instanceof nnoSubscriberResult) &&
                  (count($result->subscribers) == 1) &&
                  ($result->subscribers[0] instanceof nnoSubscriber)
                ) {
                    $record = $result->subscribers[0];
                    $data = array(
                        'first_name' => $record->christianname,
                        'last_name'  => $record->surname,
                        'phone' => $record->phone,
                        'address_line_1' => $record->address,
                        'postal_code' => $record->zipcode,
                        'city' => $record->district,
                        'countries_id' => 58,
                        'country' => 'Denmark',
                    );
                }

                break;
        }

        return $this->json_response(array(
            'status' => $error,
            'message'   => '',
            'data'  => $data,
        ));
    }
}
