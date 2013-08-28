<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Monolog;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsDomainsPricesQuery;

use Hanzo\Core\CoreController;

class RestVideoController extends CoreController
{
    public function getAction(Request $request)
    {
        $params = array(
            'video'         => $request->query->get('src'),
            'banner'        => $request->query->get('banner', 'video_bg'),
            'height'        => (int) $request->query->get('height'),
            'width'         => (int) $request->query->get('width'),
            'embed'         => (bool) $request->query->get('embed', false),
            'video_counter' => uniqid(),
        );

        return $this->render('WebServicesBundle:RestVideo:get.html.twig', $params);
    }
}
