<?php

namespace Hanzo\Core;

use Propel;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Sequences;
use Hanzo\Model\SequencesPeer;
use Hanzo\Model\SequencesQuery;

/**
 * Class Tools
 *
 * @package Hanzo\Core
 */
class Tools
{
    /**
     * Sanitize a string, trying to translate some caracters before stripping unwanted ones
     *
     * @param string  $v
     * @param string  $with
     * @param boolean $lower
     *
     * @return string
     */
    public static function stripText($v, $with = '-', $lower = true)
    {
        $urlSafeCharMap = [
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
        ];

        $search  = array_keys($urlSafeCharMap);
        $replace = array_values($urlSafeCharMap);

        $v = str_replace(' ', $with, trim($v));
        $v = str_replace($search, $replace, $v);

        $quoted = preg_quote($with);
        $v = preg_replace('/[^a-z0-9_\-'.$quoted.']+/i', '', $v);
        $v = preg_replace('/['.$quoted.']+/', '€', $v);
        $v = preg_replace('/^'.$quoted.'|'.$quoted.'|€$/', '', $v);
        $v = preg_replace('/[€-]+/', $with, $v);


        if ($lower) {
            return strtolower($v);
        }

        return $v;
    }


    /**
     * better strip tags implementation
     *
     * @param string $text
     *
     * @return string
     */
    public static function stripTags($text)
    {
        $v = preg_replace('/<+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^>]*\/*\s*>+/i', ' ', strip_tags($text));
        // Remove twig tags.
        $v = preg_replace('/(\{(\{|%)|\{\#).*(#\}|(\}|%)\})/', ' ', $v);
        $v = preg_replace('/[ ]+/', ' ', trim($v));

        return $v;
    }


    /**
     * NICETO: not hardcoded
     *
     * @param        $type
     * @param Orders $order
     *
     * @return string
     */
    public static function getBccEmailAddress($type, Orders $order)
    {
        $attributes = $order->getAttributes();

        $to = '';
        switch ($type) {
            case 'order':
                switch ($attributes->global->domain_key) {
                    case 'DE':
                    case 'SalesDE':
                        $to = 'orderde@pompdelux.com';
                        break;
                    case 'FI':
                    case 'SalesFI':
                        $to = 'orderfi@pompdelux.com';
                        break;
                    case 'NL':
                    case 'SalesNL':
                        $to = 'ordernl@pompdelux.com';
                        break;
                    case 'NO':
                    case 'SalesNO':
                        $to = 'orderno@pompdelux.com';
                        break;
                    case 'SE':
                    case 'SalesSE':
                        $to = 'orderse@pompdelux.com';
                        break;
                    case 'AT':
                    case 'SalesAT':
                        $to = 'orderat@pompdelux.com';
                        break;
                    case 'CH':
                    case 'SalesCH':
                        $to = 'orderch@pompdelux.com';
                        break;
                    default:
                        $to = 'orderdk@pompdelux.com';
                        break;
                }
                break;

            case 'retur':
                switch ($attributes->global->domain_key) {
                    case 'DE':
                    case 'SalesDE':
                        $to = 'returde@pompdelux.com';
                        break;
                    case 'FI':
                    case 'SalesFI':
                        $to = 'returfi@pompdelux.com';
                        break;
                    case 'NL':
                    case 'SalesNL':
                        $to = 'returnl@pompdelux.com';
                        break;
                    case 'NO':
                    case 'SalesNO':
                        $to = 'returno@pompdelux.com';
                        break;
                    case 'SE':
                    case 'SalesSE':
                        $to = 'returse@pompdelux.com';
                        break;
                    case 'AT':
                    case 'SalesAT':
                        $to = 'returat@pompdelux.com';
                        break;
                    case 'CH':
                    case 'SalesCH':
                        $to = 'returch@pompdelux.com';
                        break;
                    default:
                        $to = 'returdk@pompdelux.com';
                        break;
                }
                break;

            case 'rma':
                switch ($attributes->global->domain_key) {
                    case 'DE':
                    case 'SalesDE':
                        $to = 'rmade@pompdelux.com';
                        break;
                    case 'FI':
                    case 'SalesFI':
                        $to = 'rmafi@pompdelux.com';
                        break;
                    case 'NL':
                    case 'SalesNL':
                        $to = 'rmanl@pompdelux.com';
                        break;
                    case 'NO':
                    case 'SalesNO':
                        $to = 'rmano@pompdelux.com';
                        break;
                    case 'SE':
                    case 'SalesSE':
                        $to = 'rmase@pompdelux.com';
                        break;
                    case 'AT':
                    case 'SalesAT':
                        $to = 'rmaat@pompdelux.com';
                        break;
                    case 'CH':
                    case 'SalesCH':
                        $to = 'rmach@pompdelux.com';
                        break;
                    default:
                        $to = 'rmadk@pompdelux.com';
                        break;
                }
                break;
        }

        return $to;
    }


    /**
     * Sequence generator, returns next sequesce id of a named sequence.
     * Unknown sequences is created on first request.
     *
     * @param string $name the name of the sequence
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function getNextSequenceId($name)
    {
        if (!preg_match('/[a-z][a-z0-9\._ -]{2,31}/i', $name)) {
            throw new \InvalidArgumentException("'{$name}' is not a valid sequence name.");
        }

        $con = Propel::getConnection(SequencesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        $con->beginTransaction();

        $item = SequencesQuery::create()->findPk($name, $con);

        if (!$item instanceof Sequences) {
            $item = new Sequences();
            $item->setName($name);
            $item->setId(1);
        }

        $sequenceId = $item->getId();

        while (true) {
            $o = OrdersQuery::create()->findOneByPaymentGatewayId($sequenceId, $con);
            if ($o instanceof Orders) {
                $sequenceId++;
            } else {
                goto while_end;
            }
        }
        while_end: // yes labeled break...

        $item->setId($sequenceId + 1);
        $item->save($con);

        $con->commit();

        return $sequenceId;
    }


    /**
     * Wrapping the getPaymentGatewayId method to auto-generate gateway id's
     *
     * @param int $gatewayId if specified, this is used over the auto generated one
     *
     * @return int;
     */
    public static function getPaymentGatewayId($gatewayId = null)
    {
        if (is_null($gatewayId)) {
            $gatewayId = self::getNextSequenceId('payment gateway');
        }

        return $gatewayId;
    }


    /**
     * shortcut for logging data to the error log
     *
     * @param mixed   $data  the data to log
     * @param integer $back  how many levels back we dump trace for
     * @param boolean $trace set to true and the log will get a backtrace dump attached
     *
     * @return mixed
     */
    public static function log($data, $back = 0, $trace = false)
    {
        $bt = debug_backtrace();
        $line = $bt[$back]['line'];
        $root = realpath(__DIR__ . '/../../../');
        $file = str_replace($root, '~', $bt[$back]['file']);
        $data = print_r($data, 1);

        if ($trace) {
            $data .= "\ntrace:\n";
            foreach ($bt as $entry) {
                if (isset($entry['file']) && isset($entry['line'])) {
                    $data .= ' '.str_replace($root, '~', $entry['file']).' +'.$entry['line']."\n";
                }
            }
        }

        error_log($file.' +'.$line.' :: '.$data);
    }


    /**
     * debug
     *
     * Logs data send to error_log +:
     * - current customer ip
     * - current customer id (if they are logged in)
     * - current order id (if there is one)
     * - current order state (if any)
     * - current customer id on the order (if there is one)
     *
     * @param string $msg     The message to log
     * @param string $context In which context was the message generated, e.g. __METHOD__
     * @param array  $data    Key/value to dump
     *
     * @return void
     */
    public static function debug( $msg, $context, $data = [])
    {
        // we do not have access to session data here...
        if (('cli' === PHP_SAPI)) {
            return;
        }

        $order    = OrdersPeer::getCurrent();
        $customer = CustomersPeer::getCurrent();

        $out  = "-----------------------[ Debug: ".$context." ]-----------------------\n";
        $out .= $msg."\n";
        $out .= "Customer ip / id       : ". $_SERVER['REMOTE_ADDR'] ." / ". $customer->getId() ."\n";
        $out .= "Order id / state       : ". $order->getId() ." / ". $order->getState() ."\n";
        $out .= "Order customer id      : ". $order->getCustomersId() ."\n";

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = print_r($value, 1);
                }
                $out .= str_pad($key, 23).": ". $value."\n";
            }
        }

        self::log($out);
    }

    /**
     * Wrapper for php's money_format function
     *
     * @param float  $number
     * @param string $format
     *
     * @return string
     *
     * @see http://dk.php.net/manual/en/function.money-format.php
     */
    public static function moneyFormat($number, $format = '%.2i')
    {
        $number = money_format($format, (double) $number);
        if (preg_match('/^([a-z]+)\-?[0-9]/i', $number, $matches)) {
            return str_replace($matches[1], $matches[1].' ', $number);
        }

        return $number;
    }


    /**
     * Figure out wich environment to use for the requested domain
     *
     * @return string
     */
    public static function mapDomainToEnvironment()
    {
        // we use environments to switch domain configurations.
        $envMap = [
            'da_dk' => 'dk',
            'de_de' => 'de',
            'en_gb' => 'com',
            'fi_fi' => 'fi',
            'nb_no' => 'no',
            'nl_nl' => 'nl',
            'sv_se' => 'se',
            'de_at' => 'at',
            'de_ch' => 'ch',
        ];

        $path = explode('/', trim(str_replace($_SERVER['SCRIPT_NAME'], '', strtolower($_SERVER['REQUEST_URI'])), '/'));

        if (substr($path[0], 0, 9) === '_fragment') {
            // Extract the locale from the _fragment query. Hack to make ESI work on locale.
            $esiAttributes = [];
            $query = urldecode($_SERVER['QUERY_STRING']);
            parse_str($query, $esiAttributes);

            $path[0] = strtolower($esiAttributes['_locale']);
        }
        // redirect to splash screen
        elseif (empty($path[0]) || !isset($envMap[$path[0]])) {
            $path[0] = 'da_dk';
        }
        $tld = $path[0];

        if (isset($envMap[$tld])) {
            $env = $envMap[$tld];
        } else {
            $env = 'dk';
        }

        if (substr($_SERVER['HTTP_HOST'], 0, 2) == 'c.') {
            $env = $env.'_consultant';
        }

        return $env;
    }


    /**
     * handle requests for robots.txt - we autogenerate these
     */
    public static function handleRobots()
    {
        // robots only allowed on the www domain
        if (($_SERVER['REQUEST_URI'] == '/robots.txt')) {
            header('Content-type: text/plain');

            if ((substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.')) {
                die("User-agent: *\nDisallow: /\n");
            }

            die("User-agent: *\nDisallow:\n");
            // die("User-agent: *\nDisallow: /de_CH/\nDisallow: /de_AT/\n");
        }
    }


    /**
     * @param int $size
     *
     * @return string
     */
    public static function humanReadableSize($size)
    {
        $unit = ['b','kb','mb','gb','tb','pb'];

        return @round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }



    /**
     * try to get mobile useragent
     *
     * note:
     *   this is a very crude detection method, if you need anything more
     *   stable/precise you should look at http://wurfl.sourceforge.net/
     *
     * @return mixed useragent on success otherwise false.
    */
    public static function isMobileRequest()
    {
        $userAgents = [
            "iphone",         // Apple iPhone
            "ipod",           // Apple iPod touch
            "aspen",          // iPhone simulator
            "dream",          // Pre 1.5 Android
            "android",        // 1.5+ Android
            "cupcake",        // 1.5+ Android
            "blackberry9500", // Storm
            "blackberry9530", // Storm
            "opera mini",     // Experimental
            "webos",          // Experimental
            "incognito",      // Other iPhone browser
            "webmate"         // Other iPhone browser
        ];

        if (preg_match('/('.implode('|', $userAgents).')/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
            return strtolower($matches[1]);
        }

        return false;
    }


    /**
     * build and return "in order edit warning"
     *
     * @param bool $compact
     *
     * @return string
     */
    public static function getInEditWarning($compact = false)
    {
        $hanzo   = self::getHanzoInstance();
        $session = $hanzo->getSession();
        $trans   = $hanzo->container->get('translator');
        $router  = $hanzo->container->get('router');

        $params = [
            '%history_url%' => $router->generate('_account_show_order', ['order_id' => $session->get('order_id')]),
            '%order_id%'    => $session->get('order_id'),
            '%stop_url%'    => $router->generate('_account', ['stop' => 1]),
        ];

        $html = '<div id="in-edit-warning">'.$trans->trans('order.edit.global.notice', $params).'</div>';

        if ($compact) {
            $html = explode("\n", $html);
            $html = array_map('trim', $html);

            return implode('', $html);
        }

        return $html;
    }


    /**
     * helper function for setting cookies
     *
     * @param string  $name      name of the cookie
     * @param string  $value     value of the cookie
     * @param integer $ttl       cookie ttl, defaults to session cookie (0)
     * @param boolean $httpOnly  set to false if cookie is http only (ie. no javascript access)
     *
     * @return boolean
     */
    public static function setCookie($name, $value, $ttl = 0, $httpOnly = true)
    {
        static $path;

        if (PHP_SAPI == 'cli') {
            return;
        }

        if (empty($path)) {
            $path = $_SERVER['SCRIPT_NAME'];

            // dev needs the "script name" to be part of the path but prod and test does not
            if (false === strpos('.php', $_SERVER['REQUEST_URI'])) {
                $path = '';
            }

            $path .= '/'.self::getHanzoInstance()->container->get('request')->getLocale().'/';
        }

        return setcookie($name, $value, $ttl, $path, $_SERVER['HTTP_HOST'], false, $httpOnly);
    }


    /**
     * Shorthand method to remove order edit cookies.
     */
    public static function unsetEditCookies()
    {
        Tools::setCookie('__ice', '', -3600, true);
        Tools::setCookie('__ice_n', '', -3600, false);
    }


    /**
     * returns true if the request is send form a bellcom address
     * usefull when testing stuff when live
     *
     * @return boolean
     */
    public static function isBellcomRequest()
    {
        return (empty($_SERVER['REMOTE_ADDR']) || in_array($_SERVER['REMOTE_ADDR'], [
            '127.0.0.1',      // localhost
            '90.185.206.100', // office@kolding
            '87.104.21.83',   // un@home
        ]));
    }


    /**
     * detect if a request is a secure (SSL) request
     *
     * @return boolean
     */
    public static function isSecure()
    {
        $isSecure = isset($_SERVER['HTTPS']) && ('ON' == strtoupper($_SERVER['HTTPS']));

        if (!$isSecure) {
            $isSecure = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ('HTTPS' == strtoupper($_SERVER['HTTP_X_FORWARDED_PROTO']));
        }

        return $isSecure;
    }


    /**
     * image helpers
     * @NICETO move to own class...
     */

    /**
     * generates a formatted image tag.
     *
     * @see Functions::image_path()
     * @param string $src image source
     * @param string $preset the image preset to use - format heightXwidth
     * @param array $params
     *
     * @return string
     */
    public static function fxImageTag($src, $preset = '', array $params = [])
    {
        $src = self::getHanzoInstance()->get('core.cdn') . 'fx/' . $src;
        return self::generateImageTag(self::imagePath($src, $preset), $params);
    }

    /**
     * @param        $src
     * @param string $preset
     * @param array  $params
     *
     * @return string
     */
    public static function fxImageUrl($src, $preset = '', array $params = [])
    {
        $src = self::getHanzoInstance()->get('core.cdn') . 'fx/' . $src;
        return self::imagePath($src, $preset);
    }

    /**
     * @param        $src
     * @param string $preset
     * @param array  $params
     *
     * @return string
     */
    public static function productImageTag($src, $preset = '50x50', array $params = [])
    {
        $dir = 'images/products/thumb/';
        if($preset === '0x0'){
            $dir = 'images/products/';
        }
        $src = self::getHanzoInstance()->get('core.cdn2') . $dir . $src;
        return self::generateImageTag(self::imagePath($src, $preset), $params);
    }

    /**
     * @param        $src
     * @param string $preset
     * @param array  $params
     *
     * @return string
     */
    public static function productImageUrl($src, $preset = '50x50', array $params = [])
    {
        $dir = 'images/products/thumb/';
        if($preset === '0x0'){
            $dir = 'images/products/';
        }
        $src = self::getHanzoInstance()->get('core.cdn2') . $dir . $src;
        return self::imagePath($src, $preset);
    }

    /**
     * @param       $src
     * @param array $params
     *
     * @return string
     */
    public static function imageTag($src, array $params = [])
    {
        $src = self::getHanzoInstance()->get('core.cdn') . '' . $src;
        return self::generateImageTag(self::imagePath($src), $params);
    }

    /**
     * build image path based on source and preset
     *
     * @param string $src image source
     * @param string $preset the image preset to use - format heightXwidth
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function imagePath($src, $preset = '')
    {
        if ($preset && !preg_match('/[0-9]+x[0-9]+/i', $preset)) {
            throw new \InvalidArgumentException("Preset: {$preset} is not valid.");
        }

        if ($preset && $preset !== '0x0') {
            $preset .= ',';
        }else{
            $preset = '';
        }

        $url = parse_url($src);
        $file = basename($url['path']);
        $dir  = dirname($url['path']);

        $url['path'] = $dir . '/' . $preset . $file;
        $url['query'] = self::getHanzoInstance()->get('core.assets_version', 'z4');

        if (empty($url['scheme'])) {
            $url['scheme'] = 'http';
            $url['host'] = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        }

        if (self::isSecure()) {
            $url['scheme'] = 'https';
        }

        return $url['scheme'].'://'.$url['host'].$url['path'].'?'.$url['query'];
    }


    /**
     * @param string $src
     * @param array  $params
     *
     * @return string
     */
    protected static function generateImageTag($src, array $params = [])
    {
        // title and alt should never be the same...
        // if (empty($params['title']) && !empty($params['alt'])) {
        //     $params['title'] = $params['alt'];
        // }
        if (empty($params['alt']) && !empty($params['title'])) {
            $params['alt'] = $params['title'];
        }
        if (!isset($params['alt'])) {
            $params['alt'] = '';
        }

        $lazy = false;
        if (isset($params['lazy']) && (true === $params['lazy'])) {
            $lazy = true;
            unset($params['lazy']);

            if (empty($params['class'])) {
                $params['class'] = '';
            }
            $params['class'] .= ' lazy';
        }

        $noscript = false;
        if (isset($params['noscript'])) {
            $noscript = $params['noscript'];
            unset($params['noscript']);
        }

        $extra = '';

        foreach ($params as $key => $value) {
            $extra .= ' ' . $key . '="'.$value.'"';
        }

        if ($lazy) {
            if ($noscript) {
                $noscript = '<noscript><img src="' . $src . '"' . str_replace(' lazy', '', $extra) . '></noscript>';
            }
            return '<img data-original="' . $src . '"' . $extra . '>'.$noscript;
        }

        return '<img src="' . $src . '"' . $extra . '>';
    }


    /**
     * @return Hanzo
     */
    protected static function getHanzoInstance()
    {
        static $hanzo;

        if (empty($hanzo)) {
            $hanzo = Hanzo::getInstance();
        }

        return $hanzo;
    }


    /**
     * Map a domain_key to an AX endpoint
     *
     * @param $key
     * @return string
     */
    public static function domainKeyToEndpoint($key)
    {
        $key      = strtoupper(substr($key, -2));
        $endPoint = 'DK';

        switch ($key) {
            case 'AT':
            case 'CH':
            case 'DE':
            case 'FI':
            case 'NL':
            case 'NO':
            case 'SE':
                $endPoint = $key;
                break;
        }

        return $endPoint;
    }

    /**
     * Flatten array into 'key.subkey => value' sets
     *
     * @param array  $array
     * @param string $prefix
     * @return array
     */
    public static function flatten(array $array, $prefix = '')
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + self::flatten($value, $prefix.$key . '.');
            } else {
                $result[$prefix.$key] = $value;
            }
        }

        return $result;
    }
}
