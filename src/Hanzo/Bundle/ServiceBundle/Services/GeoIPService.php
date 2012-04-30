<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools
    ;

class GeoIPService
{
    protected $parameters;
    protected $settings;

    public function __construct($parameters, $settings)
    {
        $this->parameters = $parameters;
        $this->settings = $settings;
    }

    /**
     * lookup
     * @params string $ip Defaults to null and will use REMOTE_ADDR
     * @return mixed Array if result found, false on error 
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function lookup( $ip = null )
    {
        if ( is_null($ip) )
        {
          $ip = $_SERVER['REMOTE_ADDR'];
        }

        $cache = Hanzo::getInstance()->cache;
        $cache_key = $cache->id('geocache', $ip);
        $data = $cache->get($cache_key);

        if (!$data) {
            $data = array();
            $result = file_get_contents('http://geoip3.maxmind.com/b?l=Vy3Df3CSG8kI&i=' . $ip);

            if ($result) {
                $result = explode(',', $result);

                if (empty($result)) {
                    return false;
                }

                $data = array(
                    'country' => $result[0],
                    'state'   => $result[1],
                    'city'    => $result[2],
                    'lat'     => $result[3],
                    'lon'     => $result[4],
                );

                // cache the result for one week
                $cache->set($cache_key, $data, 604800);
            }
        }

        return $data;
    }
}
