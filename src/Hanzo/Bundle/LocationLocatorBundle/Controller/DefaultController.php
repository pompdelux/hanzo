<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Controller;

use Criteria;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\AddressesPeer;

class DefaultController extends CoreController
{
    public function lookupAction(Request $request)
    {
        $customer = CustomersPeer::getCurrent();

        $data = [];
        $records = [];
        $error = '';

        if ($customer instanceof Customers) {
            $c = new Criteria();
            $c->add(AddressesPeer::TYPE, 'payment');
            $address = $customer->getAddresses($c);

            if ($address->count()) {
                $address = $address->getFirst();
                $data['q'] = $address->getAddressLine1().', '.$address->getPostalCode().' '.$address->getCity();
            }
        }

        $api = $this->get('hanzo_location_locator');

        try {
            $form = $api->getLookupForm($this->createFormBuilder(), $request);
        } catch (\Exception $e) {
            return $this->response('');
        }

        if ('POST' === $request->getMethod()) {
            $values = $request->request->get('form');

            if (isset($values['q'])) {
                $pcs = explode(',', $values['q']);

                $street = $pcs[0];
                if (isset($pcs[1])) {
                    list($zip, $city) = explode(' ', $pcs[1], 2);
                } else {
                    $zip = '';
                    $city = '';
                    if (preg_match('/[0-9]+/', $street)) {
                        $zip = $street;
                        $street = '';
                    }
                }

                try {
                    $records = $api->findByAddress([
                        'countryCode'  => $values['countryCode'],
                        'city'         => $city,
                        'postalCode'   => $zip,
                        'streetName'   => $street,
                    ]);
                } catch (\Exception $e) {
                    $records = [];
                    $error = $this->get('translator')->trans('service.down', [], 'locator');
                }

                if ($this->getFormat() == 'json') {
                    if (count($records)) {
                        // hack to handle addresses where the "name" part is "N/A" - this confuses AX and Consignor.
                        foreach ($records as $k => $data) {
                            if (strtoupper($data['name']) == 'N/A') {
                                $records[$k]['name'] = $customer->getName();
                            }
                        }

                        $response = [
                            'status'  => true,
                            'message' => '',
                            'data'    => ['html' => $this->renderView('LocationLocatorBundle:Default:result.html.twig', [
                                'service' => 'postnord',
                                'records' => $records,
                            ])]
                        ];
                    } else {
                        if ($error) {
                            $message = $error;
                        } else {
                            $message = $this->get('translator')->trans('no.records.found', [], 'locator');
                        }
                        $response = [
                            'status'  => false,
                            'message' => $message,
                        ];
                    }

                    return $this->json_response($response);
                }
            }
        }

        return $this->render('LocationLocatorBundle:Default:form.html.twig', [
            'service' => 'postnord',
            'form'    => $form->createView(),
            'records' => $records,
            'error'   => $error,
        ]);
    }
}
