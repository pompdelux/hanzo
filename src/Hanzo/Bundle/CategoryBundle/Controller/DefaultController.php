<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpFoundation\Response
;

use Hanzo\Core\CoreController,
    Hanzo\Core\Tools;

use Hanzo\Model\ProductsImagesCategoriesSortQuery,
    Hanzo\Model\ProductsImagesCategoriesSortPeer,
    Hanzo\Model\CategoriesPeer
;

class DefaultController extends CoreController
{

    /**
     * handle category listings
     *
     * @param $cms_id
     * @param $category_id
     * @param $pager
     */
    public function viewAction($cms_id, $category_id, $pager = 1)
    {
        $cache_id = explode('_', $this->get('request')->get('_route'));
        $cache_id = array($cache_id[0], $cache_id[2], $cache_id[1], $pager);

        // json requests
        if ($this->getFormat() == 'json') {
            $data = $this->getCache($cache_id);
            if (!$data) {
                $data = CategoriesPeer::getCategoryProductsByCategoryId($category_id, $pager);
                $this->setCache($cache_id, $data, 5);
            }

            // for json we need the real image paths
            foreach ($data['products'] as $k => $product) {
                $data['products'][$k]['image'] = Tools::productImageUrl($product['image'], '120x240');
            }

            return $this->json_response($data);
        }

        // html/normal request
        $cache_id[] = 'html';
        $html = $this->getCache($cache_id);

        if (!$html) {
            $data = CategoriesPeer::getCategoryProductsByCategoryId($category_id, $pager);

            $this->get('twig')->addGlobal('page_type', 'category');
            $html = $this->renderView('HanzoCategoryBundle:Default:view.html.twig', $data);
            $this->setCache($cache_id, $html, 5);
        }

        return $this->response($html);
    }
}
