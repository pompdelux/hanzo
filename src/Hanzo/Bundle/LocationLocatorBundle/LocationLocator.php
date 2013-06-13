<?php

namespace Hanzo\Bundle\LocationLocatorBundle;

use InvalidArgumentException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class LocationLocator
{
    /**
     * PostNord api key
     *
     * @var string
     */
    protected $api_key;

    protected $translator;

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
     * __construct
     *
     * @param string     $api_key
     * @param Translator $translator
     */
    public function __construct($api_key, Translator $translator)
    {
        $this->api_key = $api_key;
        $this->translator = $translator;
    }


    /**
     * Find By Address
     *
     * @param  array   $address address array format is:
     *                            'countryCode'  => '', // ISO 3166-1, Alpha-2 Country code
     *                            'city'         => '',
     *                            'postalCode'   => '',
     *                            'streetName'   => '',
     *                            'streetNumber' => ''
     * @param  integer $limit   max number of elements to retrive
     * @return array
     * @throws InvalidArgumentException
     */
    public function findByAddress(array $address = [], $limit = 5)
    {
        $service = $this->services['findByAddress'];
        if (1 == count($address) && !empty($address['postalCode'])) {
            $service = $this->services['findByPostalCode'];
        }

        $data = [];
        $params = ['countryCode', 'city', 'postalCode', 'streetName', 'streetNumber'];

        foreach ($params as $key) {
            if (isset($address[$key])) {
                $data[$key] = $address[$key];
            }
        }

        if (0 === count($data)) {
            throw new InvalidArgumentException('Address lookup data missing.');
        }

        return $this->lookup($service, $data);
    }


    /**
     * Find By Location
     *
     * @param  float   $latitude
     * @param  float   $longitude
     * @param  string  $countryCode ISO 3166-1, Alpha-2 Country code
     * @param  integer $limit
     * @return array
     */
    public function findByLocation($latitude, $longitude, $countryCode, $limit = 5)
    {
        return $this->lookup($this->services['findByLocation'], [
            'northing'    => $latitude,
            'easting'     => $longitude,
            'countryCode' => $countryCode,
        ]);
    }


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
            ->add('countryCode', 'hidden', [
                'data' => substr($request->getLocale(), -2)
            ])
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
     * @param  string $service names service
     * @param  array  $data    request data
     * @return array
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
