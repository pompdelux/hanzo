<?php

namespace Hanzo\Core;

use \Propel;
use \BasePeer;

use Hanzo\Core\Hanzo,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\CustomersPeer,
    Hanzo\Model\Sequences,
    Hanzo\Model\SequencesPeer,
    Hanzo\Model\SequencesQuery
    ;

class Tools
{
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
            'billing_first_name',
            'billing_last_name',

            'delivery_countries_id',
            'delivery_state_province',
            'delivery_company_name',
            'delivery_method',
            'delivery_first_name',
            'delivery_last_name',
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
                    $address[] = 'Att: ' . trim($fields['delivery_first_name'] . ' ' . $fields['delivery_last_name']);
                } else {
                    $address[] = trim($fields['delivery_first_name'] . ' ' . $fields['delivery_last_name']);
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
     * Wrapping the getPaymentGatewayId method to auto-generate gateway id's
     *
     * @param int $gateway_id if specified, this is used over the auto generated one
     * @return $gateway_id;
     */
    public static function getPaymentGatewayId($gateway_id = null)
    {
        if (is_null($gateway_id)) {
            $gateway_id = self::getNextSequenceId('payment gateway');
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
     * debug
     *
     * Logs data send to error_log +:
     * - current customer ip
     * - current customer id (if they are logged in)
     * - current order id (if there is one)
     * - current order state (if any)
     * - current customer id on the order (if there is one)
     *
     * @param string $msg The message to log
     * @param string $context In which context was the message generated, e.g. __METHOD__
     * @param array $data Key/value to dump
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public static function debug( $msg, $context, $data = array())
    {
      $order    = OrdersPeer::getCurrent();
      $customer = CustomersPeer::getCurrent();

      $out  = "-----------------------[ Debug: ".$context." ]-----------------------\n";
      $out .= $msg."\n";
      $out .= "Customer ip / id       : ". $_SERVER['REMOTE_ADDR'] ." / ". $customer->getId() ."\n";
      $out .= "Order id / state       : ". $order->getId() ." / ". $order->getState() ."\n";
      $out .= "Order customer id      : ". $order->getCustomersId() ."\n";

      if ( !empty($data) )
      {
        foreach ($data as $key => $value)
        {
          if ( is_array($value) )
          {
            $value = print_r($value,1);
          }

          $out .= str_pad( $key, 23 ).": ". $value."\n";
        }
      }

      error_log($out);
    }

    /**
     * Wrapper for php's money_format function
     *
     * @see http://dk.php.net/manual/en/function.number-format.php
     *
     * @param float $numner
     * @param string $format see php.net for format documentation
     * @return string
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
        $env_map = array(
            'da_dk' => 'dk',
            'en_gb' => 'com',
            'fi_fi' => 'fi',
            'nb_no' => 'no',
            'nl_nl' => 'nl',
            'sv_se' => 'se',
        );

        $path = explode('/', trim(str_replace($_SERVER['SCRIPT_NAME'], '', strtolower($_SERVER['REQUEST_URI'])), '/'));

        // redirect to splash screen
        if (empty($path[0]) || !isset($env_map[$path[0]])) {
            $path[0] = 'da_dk';
        }
        $tld = $path[0];

        if (isset($env_map[$tld])) {
            $env = $env_map[$tld];
        } else {
            $env = 'dk';
        }

        if (substr($_SERVER['HTTP_HOST'], 0, 2) == 'c.') {
            $env = $env.'_consultant';
        }

        return $env;
    }

    public static function handleRobots()
    {
        // robots only allowed on the www domain
        if(($_SERVER['REQUEST_URI'] == '/robots.txt')) {
            header('Content-type: text/plain');

            if ((substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.')) {
                die("User-agent: *\nDisallow: /\n");
            }

            die("User-agent: *\nDisallow:\n");
        }
    }

    /**
     * build and return "in order edit warning"
     * @return string
     */
    public static function getInEditWarning()
    {
        $hanzo = self::getHanzoInstance();
        $session = $hanzo->getSession();
        $trans = $hanzo->container->get('translator');
        $router = $hanzo->container->get('router');

        $params = array(
            '%history_url%' => $router->generate('_account_show_order', array('order_id' => $session->get('order_id'))),
            '%order_id%' => $session->get('order_id'),
            '%stop_url%' => $router->generate('_account', array('stop' => 1)),
        );

        return '<div id="in-edit-warning">'.$trans->trans('order.edit.global.notice', $params).'</div>';
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
     * @return type
     */
    public static function fxImageTag($src, $preset = '', array $params = array())
    {
        $src = self::getHanzoInstance()->get('core.cdn') . 'fx/' . $src;
        return self::generateImageTag(self::imagePath($src, $preset), $params);
    }

    public static function productImageTag($src, $preset = '50x50', array $params = array())
    {
        $src = self::getHanzoInstance()->get('core.cdn') . 'images/products/thumb/' . $src;
        return self::generateImageTag(self::imagePath($src, $preset), $params);
    }

    public static function productImageUrl($src, $preset = '50x50', array $params = array())
    {
        $src = self::getHanzoInstance()->get('core.cdn') . 'images/products/thumb/' . $src;
        return self::imagePath($src, $preset);
    }


    public static function imageTag($src, array $params = array())
    {
        $src = self::getHanzoInstance()->get('core.cdn') . '' . $src;
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
        $url['query'] = self::getHanzoInstance()->get('core.assets_version', 'z4');

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
        if (!isset($params['alt'])) {
            $params['alt'] = '';
        }

        $extra = '';

        foreach ($params as $key => $value) {
            $extra .= ' ' . $key . '="'.$value.'"';
        }

        return '<img src="' . $src . '"' . $extra . '>';
    }

    protected static function getHanzoInstance()
    {
        static $hanzo;

        if (empty($hanzo)) {
            $hanzo = Hanzo::getInstance();
        }

        return $hanzo;
    }
}
