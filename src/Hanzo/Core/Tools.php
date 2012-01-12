<?php

namespace Hanzo\Core;

class Tools
{
    public static function stripText($v)
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

        $v = str_replace(' ', '-', trim($v));
        $v = str_replace($search, $replace, $v);

        $v = preg_replace('/[^a-z0-9_-]+/i', '', $v);
        $v = preg_replace('/[-]+/', '-', $v);
        $v = preg_replace('/^-|-$/', '-', $v);

        return strtolower($v);
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
        return self::imageTag(self::imagePath($src, $preset), $params);
    }

    public static function productImageTag($src, $preset = '50x50', array $params = array())
    {
        $src = Hanzo::getInstance()->get('core.cdn') . 'images/products/thumb/' . $src;
        return self::imageTag(self::imagePath($src, $preset), $params);
    }

    public static function productImageUrl($src, $preset = '50x50', array $params = array())
    {
        $src = Hanzo::getInstance()->get('core.cdn') . 'images/products/thumb/' . $src;
        return self::imagePath($src, $preset);
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

    protected static function imageTag($src, array $params = array())
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
