<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesCategoriesSortPeer;
use Hanzo\Model\CategoriesPeer;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\Products;

use Hanzo\Model\CmsQuery;

class ByLookController extends CoreController
{

    /**
     * handle by look listings
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
                $data = CategoriesPeer::getStylesByCategoryId($category_id, $pager);
                $this->setCache($cache_id, $data, 5);
            }

            // for json we need the real image paths
            foreach ($data['products'] as $k => $product) {
                $data['products'][$k]['image'] = Tools::productImageUrl($product['image'], '120x240');
                $data['products'][$k]['image_flip'] = Tools::productImageUrl($product['image_flip'], '120x240');
            }

            return $this->json_response($data);
        }

        // html/normal request
        $cache_id[] = 'html';
        $html = $this->getCache($cache_id);

        if (!$html) {

            $data = CategoriesPeer::getStylesByCategoryId($category_id, $pager);

            $cms_page = CmsQuery::create()->findOneById($cms_id); // Find this cms' parent's parent.
            $parent_page = CmsQuery::create()->filterById($cms_page->getParentId())->findOne();

            $this->get('twig')->addGlobal('page_type', 'look-'.$category_id);
            $this->get('twig')->addGlobal('body_classes', 'body-look look-'.$category_id);
            $this->get('twig')->addGlobal('cms_id', $parent_page->getParentId());
            $html = $this->renderView('CategoryBundle:ByLook:view.html.twig', $data);
            $this->setCache($cache_id, $html, 5);
        }

        return $this->response($html);
    }
}
