<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog;

/**
 * @see
 *  http://symfony.com/doc/2.0/cookbook/web_services/php_soap_extension.html
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *  http://speakerdeck.com/u/hhamon/p/silex-meets-soap-rest
 *
 */
class RestController extends Controller
{
    protected $request;

    public function indexAction($version, $service_name)
    {


        $response = array();
        return new Response(json_encode($response), 200, array(‘Content-type’ => ‘application/json’));
    }

    public function videoAction()
    {
        //bc_log($this->get('request'))
        $request = $this->get('request');
        $data = array(
            'video'  => $request->get('src', false),
            'width'  => $request->get('width', false),
            'height' => $request->get('height', false),
            'banner' => $request->get('banner', 'video_bg'),
            'embed'  => (boolean) $request->get('embed', 0),
        );
    }
}
