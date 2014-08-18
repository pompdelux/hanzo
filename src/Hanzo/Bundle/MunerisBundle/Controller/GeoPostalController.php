<?php

namespace Hanzo\Bundle\MunerisBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Guzzle\Http\Exception\ClientErrorResponseException;

class GeoPostalController extends CoreController
{
    protected $start;
    protected $guzzle;

    /**
     * Lookup handler for zip code and city requests
     *
     * @param  string  $country Country code (iso2)
     * @param  string  $zip     Zip code
     * @return Response          Returns a JSON encoded Response
     */
    public function lookupAction($country = '', $zip = '')
    {
        $this->start  = (microtime(true) * 1000);
        $this->guzzle = $this->get('muneris.guzzle.client');

        if (empty($country)) {
            $country = Hanzo::getInstance()->get('core.country');
        }

        // pseudo fix for 9335
        // norway have a zip code 9335, but it's a postbox so geopostal does not include this
        // the zip however is the same city as 9334
        if (($zip == 9335) && strtolower($country) == 'no') {
            $zip = 9334;
        }

        if (preg_match('/^[0-9]+$/', $zip)) {
            return $this->getByZip($country, $zip);
        }

        return $this->getByName($country, $zip);
    }

    /**
     * Find entry by zip code
     *
     * @param  string   $country Country code (iso2)
     * @param  integer  $zip     Zip code
     * @return Response          Returns a JSON encoded Response
     */
    protected function getByZip($country, $zip)
    {
        $client = $this->guzzle->get(strtr('/gpc/countries/{country}/postcodes/{zip}', [
            '{country}' => $country,
            '{zip}'     => $zip
        ]));

        return $this->send($client);
    }

    /**
     * Find entry by city name
     *
     * @param  string   $country Country code (iso2)
     * @param  integer  $city    City name
     * @return Response          Returns a JSON encoded Response
     */
    protected function getByName($country, $city)
    {
        $country = strtolower($country);
        $city    = strtolower($city);

        // we need to remap some cities, for some reason - guess people do not know where they live...
        $remap = [
            'nl' => [
                'lattrop' => 'lattrop-breklenkamp'
            ],
        ];

        if (isset($remap[$country][$city])) {
            $city = $remap[$country][$city];
        }

        $client = $this->guzzle->get(strtr('/gpc/countries/{country}/fuzies/{city}', [
            '{country}' => $country,
            '{city}'    => $city
        ]));

        return $this->send($client);
    }

    /**
     * Process the actual request to the Muneris service.
     *
     * @param  Gussle   $client Guzzle client object
     * @return Response         Returns a JSON encoded Response
     */
    protected function send($client)
    {
        $client->setHeader('Accept', 'application/json');

        try {
            $response = $client->send();
            $status   = true;
            $message  = json_decode($response->getBody());
        } catch (ClientErrorResponseException $e) {
            $status  = false;
            $message = '';
        }

        return $this->json_response([
            'status'  => $status,
            'message' => '',
            'data'    => $message,
            '_time'   => (int) ((microtime(true) * 1000) - $this->start) .'ms',
        ]);
    }
}
