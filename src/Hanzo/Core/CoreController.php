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
            $this->cache = Hanzo::getInstance()->cache;
        }

        return $this->cache->get($this->cache->id($key));
    }

    protected function setCache($key, $data, $ttl = 3600)
    {
        if (empty($this->cache)) {
            $this->cache = Hanzo::getInstance()->cache;
        }

        return $this->cache->set($this->cache->id($key), $data, $ttl);
    }

    public function response($content, $status = 200, $headers = array())
    {
        // no need to doubble "encode"
        if ($content instanceof Response) {
            return $content;
        }

        // set no cache header for json requests
        if (isset($headers['Content-Type']) &&
            ($headers['Content-Type'] == 'application/json')
        ) {
            /**
             * prevent statistics to be appended to json requests, and only set the
             * define once - it will lead to errors in sub-requests othertwise.
             */
            if (!defined('JSON_RESPONSE')) {
                define('JSON_RESPONSE', 1);
            }
            $headers['Cache-Control'] = 'no-cache';
        }

        return new Response($content, $status, $headers);
    }


    /**
     * returns a json encoded responce - with one exception
     *
     * @param mixed $data
     * @param int $code http status code, defaults to 200
     * @global $_REQUEST['_xjson'] if set the method will setup and send a x-json response.
     */
    public function json_response($data, $code = 200) {
        /**
         * this fixes an issue when working with pushState and json data
         * so to take atvantage of this, be shure to add a _xjson request parameter to your call
         */
        if ($this->get('request')->get('_xjson')) {
            header('Content-Type: application/x-json');
            header('X-JSON: ' . json_encode(array('status' => TRUE)));
            die(json_encode($data));
        }

        // only scalar values can be send as json
        if (!is_scalar($data) && (!$data instanceof Response)) {
            $data = json_encode($data);
        }

        return $this->response($data, $code, array('Content-Type' => 'application/json'));
    }
}
