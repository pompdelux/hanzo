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
    public function lookupAction(Request $request, $methodId = null)
    {
        // We grab the methodId from either the submitted form - or from injected id.
        $methodId = $request->request->get('form[method_id]', $methodId, true);
        $customer = CustomersPeer::getCurrent();

        $records = [];
        $error = '';
        $streetAddress = '';

        if ($customer instanceof Customers) {
            $c = new Criteria();
            $c->add(AddressesPeer::TYPE, 'payment');
            $address = $customer->getAddresses($c);

            if ($address->count()) {
                $address = $address->getFirst();
                $streetAddress = $address->getAddressLine1();
            }
        }

        /** @var \Hanzo\Bundle\LocationLocatorBundle\Providers\BaseProvider $api */
        $api = $this->get('hanzo_location_locator');

        // Regarding scrumdo: #1284
        // Quick fix for having multiple implementations active at the same time.
        $methodOverrides = [
            // DK PostNord overrides default Bring.
            15 => [
                'productConceptID' => 92,
                'WebShopID'        => 6,
                //'installationID'   => 90290000026,
            ],

            // SE PostNord overrides default Bring.
            31 => [
                'productConceptID' => 92,
                'WebShopID'        => 6,
                //'installationID'   => 90290000026,
            ],
        ];

        if ($methodId && isset($methodOverrides[$methodId])) {
            $api->settingsOverride($methodOverrides[$methodId]);
        }

        try {
            $form = $api->getLookupForm($this->createFormBuilder(), $request);

            if ($methodId) {
                $form->add('method_id', 'hidden', ['data' => $methodId]);
            }
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

                if (empty($street) && $streetAddress) {
                    $street = $streetAddress;
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

                if ($this->getFormat() === 'json') {
                    if (count($records)) {
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
