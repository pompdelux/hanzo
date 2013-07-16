<?php

namespace Hanzo\Bundle\MunerisBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;

class GeoPostalController extends CoreController
{
    protected $start;
    protected $guzzle;

    /**
     * Lookup handler for zip code and city requests
     *
     * @param  string   $country Country code (iso2)
     * @param  integer  $zip     Zip code
     * @return Response          Returns a JSON encoded Response
     */
    public function lookupAction($country = '', $zip = '')
    {
        $this->start  = (microtime(true) * 1000);
        $this->guzzle = $this->get('muneris.guzzle.client');

        if (empty($country)) {
            $country = Hanzo::getInstance()->get('core.country');
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
        $client = $this->guzzle->get(strtr('/gpc/countries/{country}/cities/{city}', [
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
        $response = $client->send();

        return $this->json_response([
            'status'  => true,
            'message' => '',
            'data'    => json_decode($response->getBody()),
            '_time'   => (int) ((microtime(true) * 1000) - $this->start) .'ms',
        ]);
    }
}
