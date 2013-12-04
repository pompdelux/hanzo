<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ShippingMethods;

class AddressController extends CoreController
{
    /**
     * Builds the address form based on address type.
     *
     * @param Request $request
     * @param string  $type
     * @param integer $customer_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formAction(Request $request, $type = 'payment', $customer_id = null)
    {
        $short_domain_key = substr(Hanzo::getInstance()->get('core.domain_key'), -2);
        $order = OrdersPeer::getCurrent(false);

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

        $countries          = CountriesPeer::getAvailableDomainCountries(true);
        $delivery_method_id = $order->getDeliveryMethod();

        if ($type == 'CURRENT-SHIPPING-ADDRESS') {
            if ($delivery_method_id && $order->getDeliveryFirstName()) {
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
                $address->setExternalAddressId($order->getDeliveryExternalAddressId());

                switch ($delivery_method_id) {
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
                        $address->setCompanyName($order->getDeliveryCompanyName());
                        break;
                    default:
                        $type = 'shipping';
                        $address->setType('shipping');
                        break;
                }
            } else {
                $type = 'shipping';
                $form = '<div class="block"><form action="" method="post" class="address"></form></div>';

                if ('json' === $this->getFormat()) {
                    return $this->json_response(array(
                        'status'  => true,
                        'message' => '',
                        'data'    => array('html' => $form),
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
        } elseif ('overnightbox' == $type) {
            $address->setFirstName($order->getFirstName());
            $address->setLastName($order->getLastName());
        }

        $builder = $this->createFormBuilder($address, [
            'validation_groups' => 'shipping_bundle_'.$type
        ]);

        if (in_array($type, ['company_shipping', 'overnightbox'])) {
            $label = 'company.name';
            if ($type == 'overnightbox') {
                $label = 'overnightbox.label';
            }
            $builder->add('company_name', null, [
                'label'              => $label,
                'required'           => true,
                'translation_domain' => 'account'
            ]);
        }

        if (in_array($short_domain_key, ['DE'])) {
            $builder->add('title', 'choice', [
                'choices' => [
                    'female' => 'title.female',
                    'male'   => 'title.male',
                ],
                'label'              => 'title',
                'required'           => true,
                'trim'               => true,
                'translation_domain' => 'account',
            ]);
        }

        $builder->add('first_name', null, [
            'required'           => true,
            'translation_domain' => 'account'
        ]);
        $builder->add('last_name', null, [
            'required'           => true,
            'translation_domain' => 'account'
        ]);

        if ($type == 'payment') {
            $builder->add('phone', null, [
                'required'           => true,
                'translation_domain' => 'account'
            ]);
        }

        $builder->add('address_line_1', null, [
            'required'           => true,
            'translation_domain' => 'account',
            'max_length'         => 35
        ]);

        $attr = [];
        if (in_array($short_domain_key, ['DK', 'NO', 'SE'])) {
            $attr = ['class' => 'auto-city'];
        }

        $builder->add('postal_code', null, [
            'required'           => true,
            'translation_domain' => 'account',
            'attr'               => $attr,
        ]);
        $builder->add('city', null, [
            'required'           => true,
            'translation_domain' => 'account',
            'read_only'          => (count($attr) ? true : false),
            'attr'               => ['class' => 'js-auto-city-'.$type]
        ]);

        if ('overnightbox' === $type) {
            list($country_id, $country_name) = each($countries);
            $address->setCountriesId($country_id);
            $address->setCountry($country_name);

            $builder->add('countries_id', 'hidden', ['data' => $country_id]);
            $builder->add('external_address_id', 'hidden', ['data' => $address->getExternalAddressId()]);
        } else {
            if (count($countries) > 1) {
                $builder->add('countries_id', 'choice', [
                    'empty_value'        => 'choose.country',
                    'choices'            => $countries,
                    'required'           => true,
                    'translation_domain' => 'account'
                ]);
            } else {
                list($country_id, $country_name) = each($countries);

                $address->setCountriesId($country_id);
                $address->setCountry($country_name);

                $builder->add('countries_id', 'hidden', ['data' => $country_id]);
                $builder->add('country', null, [
                    'read_only'          => true,
                    'translation_domain' => 'account'
                ]);
            }
        }

        $builder->add('customers_id', 'hidden', ['data' => $customer_id]);
        $form = $builder->getForm();

        $response = $this->render('ShippingBundle:Address:form.html.twig', [
            'type'           => $type,
            'enable_locator' => ($type != 'payment' && in_array($delivery_method_id, [12, 71])),
            'form'           => $form->createView(),
        ]);


        if ('json' === $this->getFormat()) {
            $html = $response->getContent();

            return $this->json_response([
                'status'  => true,
                'message' => '',
                'data'    => array('html' => $html),
            ]);
        }

        return $response;
    }


    /**
     * Processes the address of a given type
     *
     * @param Request $request
     * @param string  $type    Address type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function processAction(Request $request, $type)
    {
        $status = false;

        if ('POST' === $request->getMethod()) {
            // TODO: not hardcoded
            $type_map = [
                'company_shipping' => 'shipping',
                'overnightbox' => 'shipping',
            ];

            if (isset($type_map[$type])) {
                $type = $type_map[$type];
            }

            $order = OrdersPeer::getCurrent();
            $data  = $request->request->get('form');

             if ($type == 'shipping') {
                switch ($order->getDeliveryMethod()) {
                    case 11:
                        $method = 'company_shipping';
                        break;
                    case 12:
                        $method = 'overnightbox';
                        $validation_fields[] = 'company_name';
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

            if (!empty($data['title'])) {
                $address->setTitle($data['title']);
            }

            $address->setFirstName($data['first_name']);
            if (!empty($data['last_name'])) {
                $address->setLastName($data['last_name']);
            }

            $address->setAddressLine1($data['address_line_1']);

            if (!empty($data['address_line_2'])) {
                $address->setAddressLine2($data['address_line_2']);
            } else {
                $address->setAddressLine2(null);
            }

            $address->setPostalCode($data['postal_code']);
            $address->setCity($data['city']);
            $address->setStateProvince(null);

            // special rules apply for overnightbox
            if ($method == 'overnightbox') {
                $address->setCountry('Denmark');
                $address->setCountriesId(58);
                $address->setExternalAddressId($data['external_address_id']);
                $address->setAddressLine2(null);
            } else {
                $country = CountriesQuery::create()->findOneById($data['countries_id']);
                $address->setCountry($country->getName());
                $address->setCountriesId($data['countries_id']);
            }

            // remember to save the company name.
            if (in_array($method, ['company_shipping', 'overnightbox'])) {
                if (empty($data['company_name'])) {
                    $data['company_name'] = 'N/A';
                }
                $address->setCompanyName($data['company_name']);
            }

            if ('payment' == $method) {
                $customer = $address->setPhone($data['phone']);
            }


            // validate the address
            $validator = $this->get('validator');
            $translator = $this->get('translator');

            // fi uses different validation group to support different rules
            $validation_group = 'shipping_bundle_'.$method;

            $object_errors = $validator->validate($address, [$validation_group]);

            $errors = [];
            foreach ($object_errors->getIterator() as $error) {
                if (null === $error->getMessagePluralization()) {
                    $errors[] = $translator->trans(
                        $error->getMessageTemplate(),
                        $error->getMessageParameters(),
                        'validators'
                    );
                } else {
                    $errors[] = $translator->transChoice(
                        $error->getMessageTemplate(),
                        $error->getMessagePluralization(),
                        $error->getMessageParameters(),
                        'validators'
                    );
                }
            }

            if (count($errors)) {
                // needed or we cannot continue in the checkout
                $order->setAttribute('not_valid', 'global', 1);
                $order->save();

                $message = '<ul class="error"><li>'.implode('</li><li>', $errors).'</li></ul>';
                return $this->json_response(array(
                    'status' => false,
                    'message' => $message,
                ));
            }

            $address->save();

            // change phone number
            if (isset($customer) && ('payment' == $method)) {
                $customer->save();
            }

            if ($type == 'payment') {
                $order->setBillingAddress($address);
            } elseif ($type == 'shipping') {
                $order->setDeliveryAddress($address);
            }

            $order->clearAttributesByKey('not_valid');
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
