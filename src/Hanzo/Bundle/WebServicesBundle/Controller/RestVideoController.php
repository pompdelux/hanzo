<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\Products,
    Hanzo\Model\ProductsDomainsPricesQuery
;
use Hanzo\Core\CoreController;

class RestVideoController extends CoreController
{
    public function getAction()
    {
        $request = $this->get('request');

        $params = array(
            'video' => $request->get('src'),
            'banner' => $request->get('banner', 'video_bg'),
            'height' => (int) $request->get('height'),
            'width' => (int) $request->get('width'),
            'embed' => (bool) $request->get('embed', false),
            'video_counter' => uniqid(),
        );

        return $this->render('WebServicesBundle:RestVideo:get.html.twig', $params);
    }
}
