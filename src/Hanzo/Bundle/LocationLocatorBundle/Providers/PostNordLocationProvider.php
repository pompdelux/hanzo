<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Providers;

use Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class PostNordLocationProvider implements LocationProviderInterface
{
    /**
     * PostNord api key
     *
     * @var string
     */
    protected $api_key;

    /**
     * PostNord api endpoint
     *
     * @var string
     */
    protected $endpoint = 'http://api.postnord.com/wsp/rest/BusinessLocationLocator/Logistics/ServicePointService_1.0/';

    /**
     * available services
     *
     * @var array
     */
    protected $services = [
        'findByAddress'    => 'findNearestByAddress.json',
        'findByLocation'   => 'findNearestByCoordinates.json',
        'findByPostalCode' => 'findByPostalCode.json',
    ];

    /**
     * Translator instance
     *
     * @var object
     */
    protected $translator;


    /**
     * __construct
     *
     * @param array      $settings
     * @param Translator $translator
     */
    public function __construct($settings, Translator $translator)
    {
        $this->api_key = $settings['api_key'];
        $this->translator = $translator;
    }

    /**
     * Find By Address
     *
     * @param  array   $address_parts         Address array format is:
     *                                    'countryCode'  => '', // ISO 3166-1, Alpha-2 Country code
     *                                    'city'         => '',
     *                                    'postalCode'   => '',
     *                                    'streetName'   => '',
     *                                    'streetNumber' => ''
     * @param  integer $limit           Max number of elements to retrive
     * @param  integer $limit           Number of results to return
     * @throws LocatorException         If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array
     */
    public function findByAddress(array $address_parts = [], $limit = 5)
    {
        $service = $this->services['findByAddress'];
        if (1 == count($address_parts) && !empty($address_parts['postalCode'])) {
            $service = $this->services['findByPostalCode'];
        }

        $data = [];
        $params = ['countryCode', 'city', 'postalCode', 'streetName', 'streetNumber'];

        foreach ($params as $key) {
            if (isset($address_parts[$key])) {
                $data[$key] = $address_parts[$key];
            }
        }

        if (0 === count($data)) {
            throw new InvalidArgumentException('Address lookup data missing.');
        }

        return $this->lookup($service, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function findByPostalCode($postal_code, $limit = 5) {
        return $this->lookup('findByPostalCode', [
            'postalCode' => $postal_code,
            'limit' => $limit
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findByLocation($latitude, $longitude, $country_code, $limit = 5)
    {
        return $this->lookup($this->services['findByLocation'], [
            'northing'    => $latitude,
            'easting'     => $longitude,
            'countryCode' => $country_code,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getLookupForm(FormBuilder $form, Request $request)
    {
        $form = $form
            ->add('q', 'text', [
                'label'              => 'lookup.label',
                'error_bubbling'     => true,
                'translation_domain' => 'locator',
                'attr' => [
                    'placeholder'    => $this->translator->trans('lookup.placeholder', [], 'locator'),
                ]
            ])
            ->add('countryCode', 'hidden', ['data' => substr($request->getLocale(), -2)])
            ->add('provider',    'hidden', ['data' => 'postnord'])
            ->getForm()
        ;

        if (($request->getMethod() == 'POST') && !empty($_POST['form']['q'])) {
            $form->bind($request);
        }

        return $form;
    }


    /**
     * Perform the actual loockup
     *
     * @param  string   $service names service
     * @param  array    $data    request data
     */
    protected function lookup($service, array $data = [])
    {
        $data['consumerId'] = $this->api_key;
        $query = $this->endpoint.$service.'?'.http_build_query($data);

        $context = stream_context_create(['http' => [
            'header'        => 'Content-type: application/x-www-form-urlencoded; charset=utf-8',
            'method'        => 'GET',
            'timeout'       => 5,
            'max_redirects' => 0,
        ]]);

        $response = @file_get_contents($query, false, $context);

        $records = [];
        if (!is_null($response)) {
            $json = json_decode($response);


            /**
             * record:
             *     post [
             *         'id' => 123,
             *         'name' => 'some name',
             *         'address' => 'address line',
             *         'postal_code' => 'postal code',
             *         'city' => 'city name',
             *         'coordinate' => [
             *             'id' => 'xyz',
             *             'latitude' => 55.2234,
             *             'longitude' => 55.222,
             *         ],
             *         'distance' => 1234,
             *         'opening_hours' => [],
             *         'raw' => {}
             *     ]
             */

            if (isset($json->servicePointInformationResponse) && isset($json->servicePointInformationResponse->servicePoints)) {
                foreach ($json->servicePointInformationResponse->servicePoints as $point) {
                    $opening_hours = [];
                    foreach ($point->openingHours as $entry) {
                        $entry->from1 = number_format(($entry->from1 / 100), 2, ':', '');
                        $entry->to1   = number_format(($entry->to1 / 100), 2, ':', '');

                        $opening_hours[$entry->from1.' - '.$entry->to1][] = $this->translator->trans('day.'.strtolower($entry->day), [], 'locator');
                    }

                    $records[] = [
                        'id'            => $point->servicePointId,
                        'name'          => $point->name,
                        'address'       => $point->deliveryAddress->streetName . ' ' . $point->deliveryAddress->streetNumber,
                        'postal_code'   => $point->deliveryAddress->postalCode,
                        'city'          => $point->deliveryAddress->city,
                        'coordinate'    => [
                            'id'        => $point->coordinate->srId,
                            'latitude'  => number_format($point->coordinate->northing, 6, '.', ''),
                            'longitude' => number_format($point->coordinate->easting, 6, '.', ''),
                        ],
                        'distance'      => $point->routeDistance,
                        'opening_hours' => $opening_hours,
                        // 'raw' => $point,
                    ];


                }
            }
        }

        return $records;
    }
}
