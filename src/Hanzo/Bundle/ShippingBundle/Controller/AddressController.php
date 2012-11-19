<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ShippingMethods;

class AddressController extends CoreController
{
    public function formAction(Request $request, $type = 'payment', $customer_id = null)
    {
        $order = OrdersPeer::getCurrent();

        if (null === $customer_id) {
            if ($order->getCustomersId()) {
                $customer_id = $order->getCustomersId();
            } else {
                $id = CustomersPeer::getCurrent()->getId();
                if ($id) {
                    $customer_id = $id;
                }
            }
        }

        $countries = CountriesPeer::getAvailableDomainCountries(true);

        if ($type == 'CURRENT-SHIPPING-ADDRESS') {
            if (($m = $order->getDeliveryMethod()) && $order->getDeliveryFirstName()) {
                $address = new Addresses();
                $address->setCustomersId($customer_id);
                $address->setFirstName($order->getDeliveryFirstName());
                $address->setLastName($order->getDeliveryLastName());
                $address->setAddressLine1($order->getDeliveryAddressLine1());
                $address->setPostalCode($order->getDeliveryPostalCode());
                $address->setCity($order->getDeliveryCity());
                $address->setCountry($order->getDeliveryCountry());
                $address->setCountriesId($order->getDeliveryCountriesId());
                $address->setStateProvince($order->getDeliveryStateProvince());

                switch ($m) {
                    case 11:
                        $type = 'company_shipping';
                        $address->setType('company_shipping');
                        $address->setCompanyName($order->getDeliveryCompanyName());
                        break;
                    case 12:
                        $type = 'overnightbox';
                        $address->setType('overnightbox');
                        $address->setCountry('Denmark');
                        $address->setCountriesId(58);
                        $address->setStateProvince(null);
                        break;
                    default:
                        $type = 'shipping';
                        $address->setType('shipping');
                        break;
                }
            } else {
                $type = 'shipping';
                $form = '<form action="" method="post" class="address"></form>';

                if ('json' === $this->getFormat()) {
                    return $this->json_response(array(
                        'status' => true,
                        'message' => '',
                        'data' => array('html' => $form),
                    ));
                }

                return $this->response($form);
            }
        } else {
            $address = AddressesQuery::create()
              ->filterByCustomersId($customer_id)
              ->filterByType($type)
              ->findOne()
            ;
        }

        if (!$address) {
            $address = new Addresses();
            $address->setType($type);
            $address->setCustomersId($customer_id);

            if ($order->getFirstName()) {
                $address->setFirstName($order->getFirstName());
                $address->setLastName($order->getLastName());
            }
        }

        $builder = $this->createFormBuilder($address, array(
            'validation_groups' => $type
        ));

        if ('company_shipping' == $type) {
            $builder->add('company_name', null, array(
                'label' => 'company.name',
                'required' => true,
                'translation_domain' => 'account'
            ));
        }

        $builder->add('first_name', null, array('required' => true, 'translation_domain' => 'account'));
        $builder->add('last_name', null, array('required' => true, 'translation_domain' => 'account'));
        $builder->add('postal_code', null, array(
            'required' => true,
            'translation_domain' => 'account',
            'attr' => array('class' => 'auto-city'),
        ));
        $builder->add('city', null, array(
            'required' => true,
            'translation_domain' => 'account',
            'read_only' => true,
        ));

        if ('overnightbox' === $type) {
            list($country_id, $country_name) = each($countries);
            $address->setCountriesId($country_id);
            $address->setCountry($country_name);

            $builder->add('countries_id', 'hidden', array('data' => $country_id));
            $builder->add('address_line_1', null, array(
                'label' => 'overnightbox.label',
                'required' => true,
                'translation_domain' => 'account'
            ));
        } else {
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
        }

        $builder->add('customers_id', 'hidden', array('data' => $customer_id));
        $form = $builder->getForm();

        $response = $this->render('ShippingBundle:Address:form.html.twig', array(
            'type' => $type,
            'form' => $form->createView(),
        ));

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => true,
                'message' => '',
                'data' => array('html' => $response->getContent()),
            ));
        }

        return $response;
    }


    public function processAction(Request $request, $type)
    {
        $status = false;

        if ('POST' === $request->getMethod()) {
            $order = OrdersPeer::getCurrent();

            // TODO: implement some validation rules
            $data = $request->get('form');

            if ($type == 'shipping') {
                switch ($order->getDeliveryMethod()) {
                    case 11:
                        $method = 'company_shipping';
                        break;
                    case 12:
                        $method = 'overnightbox';
                        break;
                    default:
                        $method = 'shipping';
                        break;
                }
            } else {
                $method = 'payment';
            }

            $address = AddressesQuery::create()
                ->filterByCustomersId($order->getCustomersId())
                ->filterByType($method)
                ->findOne()
            ;

            if (!$address instanceof Addresses) {
                $address = new Addresses();
                $address->setCustomersId($data['customers_id']);
                $address->setType($method);
            }

            $address->setFirstName($data['first_name']);
            $address->setLastName($data['last_name']);
            $address->setAddressLine1($data['address_line_1']);
            $address->setPostalCode($data['postal_code']);
            $address->setCity($data['city']);

            // special rules apply for overnightbox
            if ($method == 'overnightbox') {
                $address->setCountry('Denmark');
                $address->setCountriesId(58);
                $address->setStateProvince(null);
            } else {
                $address->setCountry($data['country']);
                $address->setCountriesId($data['countries_id']);
                $address->setStateProvince(null);
            }

            $address->save();

            if ($type == 'payment') {
                $order->setBillingAddress($address);
            } elseif ($type == 'shipping') {
                $order->setDeliveryAddress($address);
            }

            $order->save();

            $status = true;
        }

        if ('json' === $this->getFormat()) {
            return $this->json_response(array(
                'status' => $status,
                'message' => '',
            ));
        }
    }
}
