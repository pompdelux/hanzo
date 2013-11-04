<?php

namespace Hanzo\Core;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Propel;

class CoreController extends Controller
{
    protected $shares_max_age = null;
    protected $cache;
    protected $request_format;

    protected $accepted_mimetypes = array(
        "application/json" => 'json',
        "text/javascript" => 'json',
        "text/html" => 'html',
        "*/*" => 'html'
    );

    /**
     * Maps language id's of an order to endpoint folders for pdf files, defaults to 'DK'
     * @var array
     */
    protected $pdf_language_to_code = array(
        3 => 'SE',
        4 => 'NO',
        5 => 'NL',
        6 => 'FI',
        7 => 'DE',
        8 => 'AT',
        9 => 'CH',
    );


    /**
     * Get request format
     *
     * @return string
     */
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


    /**
     * Get redis cache entry
     *
     * @param  mixed $key Cache key
     * @return mixed
     */
    protected function getCache($key)
    {
        if (empty($this->cache)) {
            $this->cache = $this->get('redis.main');
        }

        return $this->cache->get($this->cache->generateKey($key));
    }


    /**
     * Set cache entry
     *
     * @param mixed   $key  Cache key
     * @param mixed   $data Data to cache
     * @param integer $ttl  Cache lifetime
     */
    protected function setCache($key, $data, $ttl = 3600)
    {
        if (empty($this->cache)) {
            $this->cache = $this->get('redis.main');
        }

        return $this->cache->setex($this->cache->generateKey($key), $ttl, $data);
    }


    /**
     * Wrap the render method to enable us to add extra headers
     *
     * @param  string   $view       Template to render
     * @param  array    $parameters Template parameters
     * @param  Response $response   Optional Response object
     * @return Response
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $response = new Response();
        $response->setVary('X-UA-Device');

        if ($this->getSharedMaxAge() && 'webshop' == $this->get('kernel')->getSetting('store_mode')) {
            $response->setSharedMaxAge($this->getSharedMaxAge());
        }

        return parent::render($view, $parameters, $response);
    }


    /**
     * Set shared max age header
     *
     * @param integer $ttl Cache max age
     */
    public function setSharedMaxAge($ttl)
    {
        $this->shares_max_age = (int) $ttl;
    }


    /**
     * Get shared max age
     *
     * @return integer
     */
    public function getSharedMaxAge()
    {
        return $this->shares_max_age;
    }


    /**
     * Custom Response object
     *
     * @param  mixed   $content Data or Response object
     * @param  integer $status  HTTP Status code
     * @param  array   $headers Optional headers
     * @return Response
     */
    public function response($content, $status = 200, $headers = array())
    {
        // no need to doubble "encode"
        if ($content instanceof Response) {
            $content->setVary('X-UA-Device');

            if ('webshop' == $this->get('kernel')->getSetting('store_mode')) {
                if ($this->getSharedMaxAge()) {
                    $content->setSharedMaxAge($this->getSharedMaxAge());
                }
            }

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

        $headers['Vary'] = 'X-UA-Device';

        $response = new Response($content, $status, $headers);

        if ('webshop' == $this->get('kernel')->getSetting('store_mode')) {
            if ($this->getSharedMaxAge()) {
                $response->setSharedMaxAge($this->getSharedMaxAge());
            }
        }

        return $response;
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
            //header('Cache-Control: max-age=2');
            die(json_encode($data));
        }

        // only scalar values can be send as json
        if (!is_scalar($data) && (!$data instanceof Response)) {
            $data = json_encode($data);
        }

        return $this->response($data, $code, array('Content-Type' => 'application/json'));
    }


    /**
     * Shortcut for AppKernel::setTerminateEvent
     *
     * @see                      AppKernel::setTerminateEvent
     * @param string $event      event key
     * @param mixed  $parameters parameters to send to the event
     */
    public function setTerminateEvent($event, $parameters)
    {
        $this->container->get('kernel')->setTerminateEvent($event, $parameters);
    }


    /**
     * Gets the connection for which database to use
     *
     * @return Propel connection              [description]
     */
    public function getDbConnection()
    {
        if ($this->getRequest()->getSession()->get('database')) {
            return Propel::getConnection( $this->getRequest()->getSession()->get('database') , Propel::CONNECTION_WRITE );
        } else {
            return Propel::getConnection();
        }
    }


    /**
     * try to map language ids to folders, this is not a 1-1 match, so we need this little hack.
     *
     * @param  [type] $language_id [description]
     * @return [type]              [description]
     */
    protected function mapLanguageToPdfDir($language_id)
    {
        if (isset($this->pdf_language_to_code[$language_id])) {
            return $this->pdf_language_to_code[$language_id];
        }

        return 'DK';
    }
}
