<?php

namespace Hanzo\Core;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Propel;

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

    /**
     * Maps language id's of an order to endpoint folders for pdf files, defaults to 'DK'
     * @var array
     */
    protected $pdf_language_to_code = array(
        3 => 'SE',
        4 => 'NO',
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
            $this->cache = $this->get('hanzo.cache');
        }

        return $this->cache->get($this->cache->id($key));
    }

    protected function setCache($key, $data, $ttl = 3600)
    {
        if (empty($this->cache)) {
            $this->cache = $this->get('hanzo.cache');
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

    /**
     * Gets the connection for which database to use
     *
     * @return Propel connection              [description]
     */
    public function getDbConnection()
    {
        if($this->getRequest()->getSession()->get('database')){
            return Propel::getConnection( $this->getRequest()->getSession()->get('database') , Propel::CONNECTION_WRITE );
        }else{
            return Propel::getConnection();
        }
    }
}
