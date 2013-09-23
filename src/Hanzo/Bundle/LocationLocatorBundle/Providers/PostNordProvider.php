<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Providers;

use Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Post Nord Location provider
 *
 *
 * INSERT INTO domains_settings
 *   (domain_key, ns, c_key, c_value)
 * VALUES
 *   ('DK', 'locator', 'provider', 'postnord'),
 *   ('DK', 'locator', 'api_key', 'bfba285c-6719-456b-849b-79c1e27624b1');
 *
 *
 * @author Ulrik Nielsen <un@bellcom.dk>
 */
class PostNordProvider extends BaseProvider
{
    /**
     * webservice api endpoint
     *
     * @var string
     */
    protected $endpoint = 'http://api.postnord.com/wsp/rest/BusinessLocationLocator/Logistics/ServicePointService_1.0/';

    /**
     * Find By Address
     *
     * @param  array   $address_parts   Address array format is:
     *                                    'countryCode'  => '', // ISO 3166-1, Alpha-2 Country code
     *                                    'city'         => '',
     *                                    'postalCode'   => '',
     *                                    'streetName'   => '',
     *                                    'streetNumber' => ''
     * @param  integer $limit           Max number of elements to retrive
     * @param  integer $limit           Number of results to return
     * @throws ProviderException        If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array
     */
    public function findByAddress(array $address_parts = [], $limit = 5)
    {
        $service = 'findNearestByAddress.json';
        if (1 == count($address_parts) && !empty($address_parts['postalCode'])) {
            $service = 'findByPostalCode.json';
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
    public function findByPostalCode($country_code, $postal_code, $limit = 5) {
        return $this->lookup('findByPostalCode.json', [
            'countryCode' => $country_code,
            'postalCode' => $postal_code,
            'limit' => $limit
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findByLocation($latitude, $longitude, $country_code, $limit = 5)
    {
        return $this->lookup('findNearestByCoordinates.json', [
            'northing'    => $latitude,
            'easting'     => $longitude,
            'countryCode' => $country_code,
        ]);
    }

    /**
     * Perform the actual loockup
     *
     * @param  string   $service names service
     * @param  array    $data    request data
     */
    protected function lookup($service, array $data = [])
    {
        $data['consumerId'] = $this->settings['api_key'];
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
             *         'custom_id' => '123abc',
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

    /**
     * {@inheritDoc}
     */
    protected function getProviderName()
    {
        return 'postnord';
    }
}
