<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Providers;

use Exception;
use SoapClient;
use SoapHeader;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Post Nord Location provider
 *
 * INSERT INTO domains_settings
 *   (domain_key, ns, c_key, c_value)
 * VALUES
 *   ('DK', 'locator', 'provider', 'postnord'),
 *   ('DK', 'locator', 'api_key', 'bfba285c-6719-456b-849b-79c1e27624b1')
 *
 * @author Ulrik Nielsen <un@bellcom.dk>
 */
class EdiSoftProvider extends BaseProvider
{
    /**
     * locator settings
     *
     * @var array
     */
    protected $settings = [
        'username' => '',
        'password' => '',
        'installationID' => ''
    ];

    /**
     * webservice api endpoint
     *
     * @var string
     */
    protected $endpoint = 'http://qa-ws01.facility.dir.dk/ShipAdvisor/Main.asmx?WSDL';


    /**
     * {@inheritDoc}
     */
    public function findByAddress(array $address_parts = [], $limit = 5)
    {
        return $this->lookup('SearchForDropPoints', [
            'productConceptID' => 1,
            'installationID'   => $this->settings['installationID'],
            'country'          => $address_parts['countryCode'],
            'address'          => $address_parts['streetName'],
            'postCode'         => $address_parts['postalCode'],
            'city'             => $address_parts['city'],
            'limit'            => $limit,
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function findByPostalCode($country_code, $postal_code, $limit = 5)
    {
        return $this->lookup('GetClosestDropPoint', [
            'productConceptID' => 1,
            'installationID'   => $this->settings['installationID'],
            'country'          => $country_code,
            'postCode'         => $postal_code,
            'limit'            => $limit,
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function findByLocation($latitude, $longitude, $country_code, $limit = 5)
    {
        return $this->lookup('GetChODAllDropPointsOnMap', [
            'webShopId'         => 1,
            'productConceptIds' => 1,
            'customConceptIds'  => 1,
            'installationId'    => $this->settings['installationID'],
            'postCode'          => $postal_code, // FIXME ! needs to get injected somehow - without breaking the interface contract
            'country'           => $country_code,
            'mapWidth'          => 800,
            'mapHeight'         => 800,
        ]);
    }


    /**
     * Perform the actual lookup
     *
     * @param  string   $service names service
     * @param  array    $data    request data
     */
    protected function lookup($service, array $data = [])
    {
        $client = new SoapClient($this->endpoint, [
            'connection_timeout' => 5, // five seconds is a really long time ...
            'exceptions'         => true,
            'trace'              => true,
        ]);

        $client->__setSoapHeaders(new SoapHeader('SoapAuthenticator', 'ServiceAuthenticationHeader', [
            'Username' => $this->settings['username'],
            'Password' => $this->settings['password'],
        ]));

        $records = [];
        try {
            $result = $client->__call($service, $data);

            // TODO: process the response

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

        } catch (Exception $e) {
            $this->logger->err($e->getMessage());
        }

        return $records;
    }
}
