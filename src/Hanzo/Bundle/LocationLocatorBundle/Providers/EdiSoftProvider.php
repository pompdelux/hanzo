<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Providers;

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
 *   ('DK', 'locator', 'provider', 'edisoft'),
 *   ('DK', 'locator', 'username', ''),
 *   ('DK', 'locator', 'password', ''),
 *   ('DK', 'locator', 'installationID', '9029000023'),
 *   ('DK', 'locator', 'productConceptID', '')
 *
 * @author Ulrik Nielsen <un@bellcom.dk>
 */
class EdiSoftProvider extends BaseProvider
{
    protected $product_concept_map = [
        296  => ['country' => 'SE', 'name' => 'Schenker', 'method' => 'SchenkerSE_PrivpakOmbudStandard'],
        547  => ['country' => 'SE', 'name' => 'SvenskePosten', 'method' => 'SvenskePostenSE_MyPackReturn'],
        668  => ['country' => 'SE', 'name' => 'Schenker', 'method' => 'SchenkerSE_PrivpakOmbudEkonomi'],
        607  => ['country' => 'SE', 'name' => 'SvenskePosten', 'method' => 'SvenskePostenSE_MyPackUtrikes'],
        750  => ['country' => 'SE', 'name' => 'Schenker', 'method' => 'SchenkerDK_PrivpakOmdelingStandard'],
        751  => ['country' => 'SE', 'name' => 'Schenker', 'method' => 'SchenkerDK_PrivpakOmdelingØkonomi'],
        833  => ['country' => 'SE', 'name' => 'Schenker', 'method' => 'Ombud Ekonomi'],
        897  => ['country' => 'SE', 'name' => 'Schenker', 'method' => 'Ombud Ekonomi Retur'],
        904  => ['country' => 'SE', 'name' => 'Schenker', 'method' => 'Terminal'],
        911  => ['country' => 'SE', 'name' => 'Direct Link', 'method' => 'MyPack'],
        912  => ['country' => 'SE', 'name' => 'Direct Link', 'method' => 'MyPack Retur'],

        2    => ['country' => 'NO', 'name' => 'Bring', 'method' => 'Servicepakke'],
        60   => ['country' => 'NO', 'name' => 'Tollpost', 'method' => 'MyPack'],
        591  => ['country' => 'NO', 'name' => 'Tollpost', 'method' => 'Tollpost MyPack'],
        618  => ['country' => 'NO', 'name' => 'Tollpost', 'method' => 'Tollpost MyPack Retur'],
        660  => ['country' => 'NO', 'name' => 'Schenker', 'method' => 'SchenkerNO_Privpak'],
        661  => ['country' => 'NO', 'name' => 'Schenker', 'method' => 'SchenkerNO_PrivpakRetur'],
        1034 => ['country' => 'NO', 'name' => 'Bring', 'method' => 'Bedriftspakke ekspress over natt 0900'],
        1041 => ['country' => 'NO', 'name' => 'Bring', 'method' => 'Klimanøytral servicepakke'],
        1152 => ['country' => 'NO', 'name' => 'Tollpost', 'method' => 'Mypack'],

        525  => ['country' => 'FI', 'name' => 'Matkahuolto', 'method' => 'MatkahuoltoFI_NearParcel'],
        1096 => ['country' => 'FI', 'name' => 'Matkahuolto', 'method' => 'Postal Parcel'],
        1130 => ['country' => 'FI', 'name' => 'Posten Logistik SCM', 'method' => 'Mypack Ekspot'],

        92   => ['country' => 'DK', 'name' => 'Post Danmark', 'method' => 'Privatpakke'],
        254  => ['country' => 'DK', 'name' => 'Post Danmark', 'method' => 'Postopkrævingspakke'],
        571  => ['country' => 'DK', 'name' => 'Post Danmark', 'method' => 'MyPack Bulksplit'],
        749  => ['country' => 'DK', 'name' => 'Post Danmark', 'method' => 'MyPack'],
        882  => ['country' => 'DK', 'name' => 'Post Danmark', 'method' => 'Customer Return'],
    ];


    /**
     * locator settings
     *
     * @var array
     */
    protected $settings = [
        'username'         => 'Flexerman21073',
        'password'         => 'Gm15/46exRF',
        'installationID'   => 10916000028,
        'productConceptID' => 32,
        'WebShopID'        => 1,
    ];

    /**
     * max number of records to return
     *
     * @var integer
     */
    protected $limit = 5;

    /**
     * webservice api test endpoint
     * the real test is only available on demand: http://qa-ws01.facility.dir.dk/ShipAdvisor/Main.asmx?WSDL
     *
     * @var string
     */
    protected $dev_endpoint  = 'http://edi-ws01.facility.dir.dk/ShipAdvisor/main.asmx?WSDL';

    /**
     * webservice api prod endpoint
     *
     * @var string
     */
    protected $prod_endpoint = 'http://edi-ws01.facility.dir.dk/ShipAdvisor/main.asmx?WSDL';


    /**
     * {@inheritDoc}
     */
    public function findByAddress(array $address_parts = [], $limit = 5)
    {
        $this->limit = $limit;
        $client = $this->getClient();

        if (empty($address_parts['city'])) {
            $address_parts['city'] = $address_parts['streetName'];
            $address_parts['streetName'] = '';
        }
        preg_match('/^([0-9]{3,7}) (.+)/', $address_parts['city'], $result);

        if (3 == count($result)) {
            $address_parts['postalCode'] = trim($result[1]);
            $address_parts['city'] = trim($result[2]);
        }

        $lookup = [
            'productConceptID' => $this->settings['productConceptID'],
            'installationID'   => $this->settings['installationID'],
            'country'          => $address_parts['countryCode'],
            'address'          => $address_parts['streetName'],
            'postCode'         => $address_parts['postalCode'],
            'city'             => $address_parts['city'],
            'limit'            => $limit,
        ];

        try {
            $result = $client->SearchForDropPoints($lookup);
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return [];
        }

        return $this->parseResult($result, 'SearchForDropPointsResult');
    }


    /**
     * {@inheritDoc}
     */
    public function findByPostalCode($country_code, $postal_code, $limit = 5)
    {
        $this->limit = $limit;
        $client = $this->getClient();

        try {
            $result = $client->SearchForDropPoints([
                'productConceptID' => $this->settings['productConceptID'],
                'installationID'   => $this->settings['installationID'],
                'country'          => $country_code,
                'postCode'         => $postal_code,
                'limit'            => $limit,
            ]);
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return [];
        }

        return $this->parseResult($result, 'SearchForDropPointsResult');
    }


    /**
     * {@inheritDoc}
     */
    public function findByLocation($latitude, $longitude, $country_code, $limit = 5)
    {
        $this->limit = $limit;

        $client = $this->getClient();

        try {
            $result = $client->GetChODAllDropPointsOnMap([
                'webShopId'         => 1, // ??
                'productConceptIds' => 1, // ??
                'webshopProductIds' => 1, // ??
                'installationId'    => $this->settings['installationID'],
                'postCode'          => $postal_code, // FIXME ! needs to get injected somehow - without breaking the interface contract
                'country'           => $country_code,
                'mapWidth'          => 800,
                'mapHeight'         => 800,
            ]);
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return [];
        }

        return $this->parseResult($result, 'GetChODAllDropPointsOnMapResult');
    }


    /**
     * Perform the actual lookup
     *
     * @param  string   $service names service
     * @param  array    $data    request data
     */
    protected function getClient()
    {
        static $client;

        if (empty($client)) {
            $endpoint = (in_array(substr($this->environment, 0, 4), ['prod', 'test']))
                ? $this->prod_endpoint
                : $this->dev_endpoint
            ;

            $client = new \SoapClient($endpoint, [
                'connection_timeout' => 5, // five seconds is a really long time ...
                'exceptions'         => true,
                'trace'              => true,
            ]);

            $client->__setSoapHeaders(new \SoapHeader('SoapAuthenticator', 'ServiceAuthenticationHeader', [
                'Username'    => $this->settings['username'],
                'Password'    => $this->settings['password'],
                'IsEncrypted' => false,
            ]));
        }

        return $client;
    }


    /**
     * {@inheritDoc}
     */
    protected function getProviderName()
    {
        return 'edisoft';
    }


    /**
     * Parse the SOAP result and return standardized array
     *
     * @param  stdClass $result The SOAP result class
     * @param  string   $key    Result key name
     * @return array            List of locations
     */
    protected function parseResult($result, $key)
    {
        $set = [];

        if (!empty($result->$key->DropPointData)) {

            $i=0;
            foreach ($result->$key->DropPointData as $record) {
                $set[$i] = [
                    'id'            => $record->OriginalId,
                    'custom_id'     => $record->ESId,
                    'name'          => $record->Name1,
                    'address'       => $record->Address1,
                    'postal_code'   => $record->PostalCode,
                    'city'          => $record->City,
                    'country_code'  => $record->CountryCode,
                    'opening_hours' => [],
                    'coordinate'    => [
                        'longitude' => number_format($record->MapRefX, 6, '.', ''),
                        'latitude'  => number_format($record->MapRefY, 6, '.', ''),
                    ],
                    'raw'           => $record,
                ];

                if (!empty($record->Phone)) {
                    $set[$i]['phone'] = $record->Phone;
                }

                if (!empty($record->Distance)) {
                    $set[$i]['distance'] = number_format(1000 * $record->Distance, 6, '.', '');
                }

                $i++;
            }
        }

        return $set;
    }
}
