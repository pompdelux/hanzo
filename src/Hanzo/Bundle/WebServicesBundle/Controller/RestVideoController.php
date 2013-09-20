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
        if ('POST' == $request->getMethod()) {
            $video  = $request->request->get('src');
            $banner = $request->request->get('banner', 'video_bg');
            $height = $request->request->get('height');
            $width  = $request->request->get('width');
            $embed  = $request->request->get('embed', false);
        } else {
            $video  = $request->query->get('src');
            $banner = $request->query->get('banner', 'video_bg');
            $height = $request->query->get('height');
            $width  = $request->query->get('width');
            $embed  = $request->query->get('embed', false);
        }

        $params = array(
            'video'         => $video,
            'banner'        => $banner,
            'height'        => (int) $height,
            'width'         => (int) $width,
            'embed'         => (bool) $embed,
            'video_counter' => uniqid(),
        );

        return $this->render('WebServicesBundle:RestVideo:get.html.twig', $params);
    }
}
