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

            $this->get('twig')->addGlobal('page_type', 'category-'.$category_id);
            $this->get('twig')->addGlobal('body_classes', 'body-category category-'.$category_id);
            $html = $this->renderView('CategoryBundle:Default:view.html.twig', $data);
            $this->setCache($cache_id, $html, 5);
        }

        return $this->response($html);
    }

    public function listProductsAction($view = 'simple', $filter = 'G_')
    {
        $filter_map = array(
            'G_' => 'Girl',
            'LG_' => 'Little Girl',
            'B_' => 'Boy',
            'LB_' => 'Little Boy',
        );

        $hanzo = Hanzo::getInstance();
        $domain_id = $hanzo->get('core.domain_id');

        $products = ProductsQuery::create()
            ->where('products.MASTER IS NULL')
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId($domain_id)
            ->endUse()
            ->useProductsToCategoriesQuery()
                ->useCategoriesQuery()
                    ->filterByContext($filter.'%', \Criteria::LIKE)
                ->endUse()
            ->endUse()
            ->joinWithProductsToCategories()
            ->orderBySku()
            ->groupBySku()
            ->find()
        ;

        $records = array();
        foreach ($products as $product) {
            $records[] = array(
                'sku' => $product->getSku(),
                'id' => $product->getId(),
                'title' => $product->getSku(),
            );
        }

        $max = ceil(count($records)/3);
        $records = array_chunk($records, $max);

        return $this->render('CategoryBundle:Default:contextList.html.twig', array(
            'page_type' => 'context-list',
            'products' => $records,
            'page_title' => $filter_map[$filter]
        ));
    }
}
