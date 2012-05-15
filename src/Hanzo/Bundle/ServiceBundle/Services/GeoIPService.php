<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\CountriesQuery;

use Symfony\Component\HttpFoundation\Request;

class GeoIPService
{
    protected $parameters;
    protected $settings;
    protected $request;

    public function __construct($parameters, $settings)
    {
        if (!$parameters[0] instanceof Request) {
            throw new \InvalidArgumentException('Request object expected as first parameter.');
        }

        $this->request = array_shift($parameters);
        $this->parameters = $parameters;
        $this->settings = $settings;
    }

    /**
     * lookup
     *
     * @param string $ip Defaults to null and will use REMOTE_ADDR
     * @return mixed Array if result found, false on error
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function lookup( $ip = null )
    {
        if (is_null($ip)) {
          $ip = $this->request->getClientIp();
          // For local testing:
          // $ip = '90.185.183.84';
        }

        $cache = Hanzo::getInstance()->cache;
        $cache_key = $cache->id('geocache', $ip);
        $data = $cache->get($cache_key);

        if (!$data) {
            $data = array();
            $result = file_get_contents('http://geoip3.maxmind.com/b?l=Vy3Df3CSG8kI&i=' . $ip);

            if ($result) {
                $result = explode(',', $result);

                if ( empty($result) || count($result) < 5) {
                    return false;
                }

                $record = CountriesQuery::create()
                    ->filterByIso2( $result[0] )
                    ->findOne()
                    ;

                $countryID        = null;
                $countryName      = null;
                $countryLocalName = null;
                if ( !is_null($record) )
                {
                    $countryID        = $record->getId();
                    $countryName      = $record->getName();
                    $countryLocalName = $record->getLocalName();
                }

                $data = array(
                    'country'           => $result[0],
                    'country_name'      => $countryName,
                    'country_localname' => $countryLocalName,
                    'country_id'        => $countryID,
                    'state'             => $result[1],
                    'city'              => $result[2],
                    'lat'               => $result[3],
                    'lon'               => $result[4],
                );

                // cache the result for one week
                $cache->set($cache_key, $data, 604800);
            }
        }

        return $data;
    }
}
