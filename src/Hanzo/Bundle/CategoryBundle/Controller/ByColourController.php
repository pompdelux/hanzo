<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Criteria;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\CategoriesI18nQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsImagesPeer;
use Hanzo\Model\ProductsToCategoriesPeer;
use Hanzo\Model\ProductsToCategoriesQuery;

class ByColourController extends CoreController
{
    public function viewAction($id)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');
        $route = $container->get('request')->get('_route');

        $router = $container->get('router');

        $cache_id = explode('_', $this->get('request')->get('_route'));
        $cache_id = array($cache_id[0], $cache_id[2], $cache_id[1]);

        // html/normal request
        $cache_id[] = 'html';
        $html = $this->getCache($cache_id);

        if (!$html) {
            $page = CmsPeer::getByPK($id, $locale);
            $settings = json_decode($page->getSettings());

            $includes = explode(',', $settings->category_ids);
            $ignores = explode(',', $settings->ignore);
            $color_map = explode(',', $settings->colors);

            $categories = array();
            $resultset = CategoriesI18nQuery::create()
                ->filterByLocale($locale)
                ->findById($includes)
            ;
            foreach ($resultset as $category) {
                $categories[$category->getId()] = $category->getTitle();
            }
            unset ($resultset);

            $index = 1;
            $products = array();
            $masters = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                    ->filterBySku($ignores, Criteria::NOT_IN)
                    ->filterByMaster(null, Criteria::ISNULL)
                    ->useProductsDomainsPricesQuery()
                        ->filterByDomainsId($domain_id)
                    ->endUse()
                ->endUse()
                ->joinWithProducts()
                ->groupByProductsId()
                ->filterByCategoriesId($includes)
                ->addAscendingOrderByColumn(sprintf(
                    "FIELD(%s, %s)",
                    ProductsToCategoriesPeer::CATEGORIES_ID,
                    implode(',', $includes)
                ))
                ->find()
            ;
            $skus = array();
            $ids = array();
            $products_to_categories = array();
            foreach ($masters as $master) {
                $skus[$master->getProducts()->getSku()] = $categories[$master->getCategoriesId()];
                $ids[] = $master->getProducts()->getId();
                $products_to_categories[$master->getProducts()->getId()] = $master->getCategoriesId();
            }

            $variants = ProductsImagesQuery::create()
                ->joinWithProducts()
                ->useProductsQuery()
                    ->filterByMaster(null, Criteria::ISNULL)
                    ->useProductsToCategoriesQuery()
                        ->addDescendingOrderByColumn(sprintf(
                            "FIELD(%s, %s)",
                            ProductsToCategoriesPeer::CATEGORIES_ID,
                            implode(',', $includes)
                        ))
                    ->endUse()
                    ->filterById($ids)
                ->endUse()
                ->addDescendingOrderByColumn(sprintf(
                    "FIELD(%s, %s)",
                    ProductsImagesPeer::COLOR,
                    '\''.implode('\',\'', $color_map).'\''
                ))
                ->filterByType('overview')
                ->find()
            ;

            $product_route = str_replace('bycolour_', 'product_', $route);

            foreach ($variants as $variant) {
                $product = $variant->getProducts();

                $products[] = array(
                    'category_id' => $products_to_categories[$variant->getProducts()->getId()],
                    'sku' => $product->getSku(),
                    'id' => $product->getId(),
                    'title' => $product->getSku(),
                    'color' => $variant->getColor(),
                    'image' => $variant->getImage(),
                    'image_flip' => str_replace('_overview_', '_set_', $variant->getImage()),
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getSku()),
                        'focus' => $variant->getId()
                    )),
                    'index' => $index,
                );

                $index++;
            }

            // get product prices
            $prices = ProductsDomainsPricesPeer::getProductsPrices($ids);

            // attach the prices to the products
            foreach ($products as $i => $data) {
                if (isset($prices[$data['id']])) {
                    $products[$i]['prices'] = $prices[$data['id']];
                }
            }

            $parent_page = CmsQuery::create()->filterById($page->getParentId())->findOne();

            $this->get('twig')->addGlobal('page_type', 'bycolour-'.$page->getTitle());
            $this->get('twig')->addGlobal('body_classes', 'body-bycolour bycolour-'.$page->getTitle());
            $this->get('twig')->addGlobal('show_new_price_badge', 1);
            $this->get('twig')->addGlobal('cms_id', $parent_page->getParentId());
            $html = $this->renderView('CategoryBundle:ByColour:view.html.twig', array('products' => $products));
            $this->setCache($cache_id, $html, 5);
        }

        return $this->response($html);
    }
}
