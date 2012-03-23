<?php

namespace Hanzo\Core;

use Propel;
use Hanzo\Core\Hanzo;
use Hanzo\Model\Orders;
use Hanzo\Model\Sequences;
use Hanzo\Model\SequencesPeer;
use Hanzo\Model\SequencesQuery;

class Tools
{
    /**
     * Get country, state city lat/lon information from an ip address
     * The method relies on maxminds webservice for ip to country database.
     *
     * @param string $ip
     * @return array
     */
    public function getIp($ip)
    {
        $cache = Hanzo::getInstance()->cache;
        $cache_key = $cache->id('geocache', $ip);
        $data = $cache->get($cache_key);

        if (!$data) {
            $data = array();
            $result = file_get_contents('http://geoip3.maxmind.com/b?l=Vy3Df3CSG8kI&i=' . $ip);

            if ($result) {
                $result = explode(',', $result);
                $data = array(
                    'country' => $result[0],
                    'state' => $result[1],
                    'city' => $result[2],
                    'lat' => $result[3],
                    'lon' => $result[4],
                );

                // cache the result for one week
                $cache->set($cache_key, $data, 604800);
            }
        }

        return $data;
    }


    /**
     * Sanitize a string, trying to translate some caracters before stripping unwanted ones
     *
     * @param string $v
     * @return string
     */
    public static function stripText($v, $with = '-', $lower = true)
    {
        $url_safe_char_map = array(
            'æ' => 'ae', 'Æ' => 'AE',
            'ø' => 'oe', 'Ø' => 'OE',
            'å' => 'aa', 'Å' => 'AA',
            'é' => 'e',  'É' => 'E', 'è' => 'e', 'È' => 'E',
            'à' => 'a',  'À' => 'A', 'ä' => 'a', 'Ä' => 'A', 'ã' => 'a', 'Ã' => 'A',
            'ò' => 'o',  'Ò' => 'O', 'ö' => 'o', 'Ö' => 'O', 'õ' => 'o', 'Õ' => 'O',
            'ù' => 'u',  'Ù' => 'U', 'ú' => 'u', 'Ú' => 'U', 'ũ' => 'u', 'Ũ' => 'U',
            'ì' => 'i',  'Ì' => 'I', 'í' => 'i', 'Í' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I',
            'ß' => 'ss',
            'ý' => 'y', 'Ý' => 'Y',
            ' ' => '-',
            '/' => '-',
        );

        $search  = array_keys($url_safe_char_map);
        $replace = array_values($url_safe_char_map);

        $v = str_replace(' ', $with, trim($v));
        $v = str_replace($search, $replace, $v);

        $v = preg_replace('/[^a-z0-9_-]+/i', '', $v);
        $v = preg_replace('/['.$with.']+/', $with, $v);
        $v = preg_replace('/^'.$with.'|'.$with.'$/', '', $v);

        if ($lower) {
            return strtolower($v);
        }

        return $v;
    }

    public static function stripTags($text)
    {
        return preg_replace('/<+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^>]*\/*\s*>+/i', '', $text);
    }


    /**
     * Format order addresses
     *
     * @param  string $part  wich address part to format, can be either 'payment' or 'shipping'
     * @param  Orders $order the order object
     * @return string        the formatted address
     */
    public static function orderAddress($part, Orders $order)
    {
        static $cache = array();

        $id = $order->getId();
        if (empty($cache[$id])) {
            $cache[$id] = $order->toArray(BasePeer::TYPE_FIELDNAME);
        }

        $fields = $cache[$id];
        $address = array();

        $skip = array(
            'billing_countries_id',
            'billing_method',

            'delivery_countries_id',
            'delivery_state_province',
            'delivery_company_name',
            'delivery_method',
        );

        switch ($part) {
            case 'billing':
            case 'payment':
                $address[] = trim($fields['first_name'] . ' ' . $fields['last_name']);
                foreach ($fields as $key => $value) {
                    if (!in_array($key, $skip) && $value && is_scalar($value) && (substr($key, 0, 8) == 'billing_')) {
                        $address[$key] = $value;
                    }
                }
                $address['billing_postal_code'] = $address['billing_postal_code'] . ' ' . $address['billing_city'];
                unset($address['billing_city']);
            break;
            case 'delivery':
            case 'shipping':
                if ($fields['delivery_company_name']) {
                    $address[] = $fields['delivery_company_name'];
                    $address[] = 'Att: ' . trim($fields['first_name'] . ' ' . $fields['last_name']);
                } else {
                    $address[] = trim($fields['first_name'] . ' ' . $fields['last_name']);
                }

                foreach ($fields as $key => $value) {
                    if (!in_array($key, $skip) && $value && is_scalar($value) && (substr($key, 0, 9) == 'delivery_')) {
                        $address[$key] = $value;
                    }
                }
                $address['delivery_postal_code'] = $address['delivery_postal_code'] . ' ' . $address['delivery_city'];
                unset($address['delivery_city']);
            break;
        }

        return implode("\n", $address);
    }

    /**
     * Sequence generator, returns next sequesce id of a named sequence.
     * Unknown sequences is created on first request.
     *
     * @param  string $name the name of the sequence
     * @return int
     */
    public static function getNextSequenceId($name)
    {
        if (!preg_match('/[a-z][a-z0-9\._ -]{2,31}/i', $name)) {
            throw new \InvalidArgumentException("'{$name}' is not a valid sequence name.");
        }

        Propel::setForceMasterConnection(true);
        $con = Propel::getConnection(SequencesPeer::DATABASE_NAME);
        $con->beginTransaction();

        $item = SequencesQuery::create()->findPk($name);

        if (!$item instanceof Sequences) {
            $item = new Sequences();
            $item->setName($name);
            $item->setId(1);
        }

        $sequence_id = $item->getId();

        $item->setId($sequence_id + 1);
        $item->save();

        $con->commit();
        Propel::setForceMasterConnection(false);

        return $sequence_id;
    }

    /**
     * Wrapping the setPaymentGatewayId method to auto-generate gateway id's
     *
     * @param int $gateway_id if specified, this is used over the auto generated one
     * @return $gateway_id;
     */
    public static function getPaymentGatewayId($gateway_id = null)
    {
        if (is_null($gateway_id)) {
            $gateway_id = self::getNextSequenceId('payment gateway');

            $env = Hanzo::getInstance()->get('kernel')->getEnvironment();
            if ($env != 'prod') {
                $gateway_id = $env . '_' . $gateway_id;
            }
        }

        return $gateway_id;
    }


    /**
     * shortcut for logging data to the error log
     * only requests comming from bellcom ip addresses will be logged.
     *
     * @param mixed $data the data to log
     * @param integer $back how many levels back we dump trace for
     */
    public static function log($data, $back = 0) {
        $bt = debug_backtrace();
        $line = $bt[$back]['line'];
        $file = str_replace(realpath(__DIR__ . '/../../../'), '~', $bt[$back]['file']);

        error_log($file.' +'.$line.' :: '.print_r($data, 1));
    }


    /**
     * Wrapper for php's money_fornat function
     *
     * @see http://dk.php.net/manual/en/function.number-format.php
     *
     * @param float $numner
     * @param string $format see php.net for format documentation
     * @return string
     */
    public static function moneyFormat($number, $format = '%i')
    {
        return money_format($format, $number);
    }


    /**
     * image helpers
     * @todo move to own class...
     */

    /**
     * generates a formatted image tag.
     *
     * @see Functions::image_path()
     * @param string $src image source
     * @param string $preset the image preset to use - format heightXwidth
     * @param array $params
     * @return type
     */
    public static function fxImageTag($src, $preset = '', array $params = array())
    {
        $src = Hanzo::getInstance()->get('core.cdn') . 'fx/' . $src;
        return self::generateImageTag(self::imagePath($src, $preset), $params);
    }

    public static function productImageTag($src, $preset = '50x50', array $params = array())
    {
        $src = Hanzo::getInstance()->get('core.cdn') . 'images/products/thumb/' . $src;
        return self::generateImageTag(self::imagePath($src, $preset), $params);
    }

    public static function productImageUrl($src, $preset = '50x50', array $params = array())
    {
        $src = Hanzo::getInstance()->get('core.cdn') . 'images/products/thumb/' . $src;
        return self::imagePath($src, $preset);
    }


    public static function imageTag($src, array $params = array())
    {
        $src = Hanzo::getInstance()->get('core.cdn') . '' . $src;
        return self::generateImageTag(self::imagePath($src), $params);
    }

    /**
     * build image path based on source and preset
     *
     * @param string $src image source
     * @param string $preset the image preset to use - format heightXwidth
     * @throws InvalidArgumentException
     * @return string
     */
    public static function imagePath($src, $preset = '')
    {
        if ($preset && !preg_match('/[0-9]+x[0-9]+/i', $preset)) {
            throw new \InvalidArgumentException("Preset: {$preset} is not valid.");
        }

        if ($preset) {
            $preset .= ',';
        }

        $url = parse_url($src);
        $file = basename($url['path']);
        $dir  = dirname($url['path']);

        $url['path'] = $dir . '/' . $preset . $file;
        $url['query'] = Hanzo::getInstance()->get('core.cache_key', 'z4');

        if (empty($url['scheme'])) {
            $url['scheme'] = 'http';
            $url['host'] = $_SERVER['HTTP_HOST'];
        }

        return $url['scheme'].'://'.$url['host'].$url['path'].'?'.$url['query'];
    }

    protected static function generateImageTag($src, array $params = array())
    {
        if (empty($params['title']) && !empty($params['alt'])) {
            $params['title'] = $params['alt'];
        }
        if (empty($params['alt']) && !empty($params['title'])) {
            $params['alt'] = $params['title'];
        }

        $extra = '';

        foreach ($params as $key => $value) {
            $extra .= ' ' . $key . '="'.$value.'"';
        }

        return '<img src="' . $src . '"' . $extra . '>';
    }
}
