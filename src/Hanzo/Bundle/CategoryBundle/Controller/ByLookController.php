<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     * @param integer $cms_id
     * @param integer $category_id
     * @param integer $pager
     * @return Response
     */
    public function viewAction(Request $request, $cms_id, $category_id, $pager = 1)
    {
        $cache_id = explode('_', $request->get('_route'));
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
                $data['products'][$k]['image'] = Tools::productImageUrl($product['image'], '234x410');
            }

            $data['products'] = $this->setAlt($data['products'], $category_id);

            return $this->json_response($data);
        }

        // html/normal request
        $cache_id[] = 'html';
        $html = $this->getCache($cache_id);

        if (!$html) {
            $data = CategoriesPeer::getStylesByCategoryId($category_id, $pager);
            $data['products'] = $this->setAlt($data['products'], $category_id);
            $cms_page = CmsQuery::create()->findOneById($cms_id);


            $classes = 'bylook-'.preg_replace('/[^a-z]/', '-', strtolower($cms_page->getTitle()));
            if (preg_match('/(pige|girl|tjej|tytto|jente)/', $request->getPathInfo())) {
                $classes .= ' category-girl';
            } elseif (preg_match('/(dreng|boy|kille|poika|gutt)/', $request->getPathInfo())) {
                $classes .= ' category-boy';
            }

            $this->get('twig')->addGlobal('page_type', 'bylook-'.$category_id);
            $this->get('twig')->addGlobal('body_classes', 'body-bylook bylook-'.$category_id.' '.$classes);
            $this->get('twig')->addGlobal('cms_id', $cms_page->getParentId());

            $html = $this->renderView('CategoryBundle:ByLook:view.html.twig', $data);
            $this->setCache($cache_id, $html, 5);
        }

        return $this->response($html);
    }

    protected function setAlt($products, $category_id)
    {
        $translator = $this->container->get('translator');

        foreach ($products as $k => $product) {
            $data['products'][$k]['alt'] = trim(Tools::stripTags($translator->trans('headers.bylook-'.$category_id, [], 'category'))).': '.$product['sku'];
        }

        return $products;
    }
}
