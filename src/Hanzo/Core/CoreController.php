<?php

namespace Hanzo\Core;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CoreController extends Controller
{
    protected $cache;
    protected $request_format;

    protected $accepted_mimetypes = array(
        "application/json" => 'json',
        "text/javascript" => 'json',
        "text/html" => 'html',
        "*/*" => 'html'
    );


    protected function getFormat()
    {
        if (empty($this->request_format)) {
            $this->request_format = 'html';
            foreach ($this->get('request')->getAcceptableContentTypes() as $mimetype) {
                if (isset($this->accepted_mimetypes[$mimetype])) {
                    $this->request_format = $this->accepted_mimetypes[$mimetype];
                    break;
                }
            }
        }

        return $this->request_format;
    }

    protected function getCache($key)
    {
        if (empty($this->cache)) {
            $this->cache = new RedisCache($this->container);
        }

        return $this->cache->get($this->cache->id($key));
    }

    protected function setCache($key, $data, $ttl = 3600)
    {
        if (empty($this->cache)) {
            $this->cache = new RedisCache($this->container);
        }

        return $this->cache->set($this->cache->id($key), $data, $ttl);
    }

    public function response($content, $status = 200, $headers = array())
    {
        // set no cache header for json requests
        if (isset($headers['Content-Type']) &&
            ($headers['Content-Type'] == 'application/json')
        ) {
            define('JSON_RESPONCE', 1);
            $headers['Cache-Control'] = 'no-cache';
        }

        return new Response($content, $status, $headers);
    }

    public function json_responce($data) {
        return $this->response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }


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
}
