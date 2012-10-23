<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Model\ShippingMethods;

class AddressController extends CoreController
{
    public function formAction(Request $request, $type = 'payment', $customer_id = null)
    {
        if (null === $customer_id) {
            $order = OrdersPeer::getCurrent();
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

        $address = AddressesQuery::create()
          ->filterByCustomersId($customer_id)
          ->filterByType($type)
          ->findOne()
        ;

        if (!$address) {
            $address = new Addresses();
            $address->setType($type);
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
        $builder->add('postal_code', null, array('required' => true, 'translation_domain' => 'account'));
        $builder->add('city', null, array('required' => true, 'translation_domain' => 'account'));

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

    public function processAction(Request $request)
    {
        if ('POST' === $request->getMethod()) {
            // handle address post
        }
    }
}
