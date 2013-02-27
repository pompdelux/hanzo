<?php

namespace Hanzo\Bundle\LocationLocatorBundle;

use Symfony\Component\HttpFoundation\Response;

class LocationLocator
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
     * __construct
     *
     * @param string $api_key
     */
    public function __construct($api_key)
    {
        $this->api_key = $api_key;
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
     * @param  float   $northing (lat)
     * @param  float   $easting  (lon)
     * @param  string  $countryCode ISO 3166-1, Alpha-2 Country code
     * @param  integer $limit
     * @return array
     */
    public function findByLocation($northing, $easting, $countryCode, $limit = 5)
    {
        return $this->lookup($this->services['findByLocation'], [
            'northing'    => $northing,
            'easting'     => $easting,
            'countryCode' => $countryCode,
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
        $data['consumerId'] = $this->api_key;
        $query = $this->endpoint.$service.'?'.http_build_query($data);

        $context = stream_context_create(['http' => [
            'header'        => 'Content-type: application/x-www-form-urlencoded; charset=utf-8',
            'method'        => 'GET',
            'timeout'       => 5,
            'max_redirects' => 0,
        ]]);

        $response = @file_get_contents($query, false, $context);

if (!is_null($response)) {
    $json = json_decode($response);

    $service_points = [];
    if (isset($json->servicePointInformationResponse) && isset($json->servicePointInformationResponse->servicePoints)) {
        foreach ($json->servicePointInformationResponse->servicePoints as $service_point) {
            if (isset($service_points[$service_point->servicePointId])) {
                continue;
            }

            $opening_hours = [];
            foreach ($service_point->openingHours as $entry) {
                $opening_hours[$entry->from1.'|'.$entry->to1][] = $entry->day;
            }
            $service_point->openingHours = $opening_hours;

            $service_points[$service_point->servicePointId] = $service_point;
        }
    }
}

return $service_points;



        // $headers = [];
        // $status_code = 200;

        // foreach ($http_response_header as $line) {
        //     if ('HTTP/' === substr($line, 0, 5)) {
        //         $status_code = substr($line, 9, 3);
        //         continue;
        //     }
        //     list ($key, $value) = explode(':', $line, 2);

        //     $headers[$key] = trim($value);
        // }

        // return new Response($response, $status_code, $headers);
    }
}
