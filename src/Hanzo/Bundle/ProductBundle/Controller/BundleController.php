<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Criteria;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\Stock;
use Hanzo\Core\CoreController;

use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\ProductsStockPeer;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStock;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsWashingInstructions;
use Hanzo\Model\ProductsWashingInstructionsQuery;
use Hanzo\Model\ProductsToCategoriesQuery;

class BundleController extends CoreController
{
    public function viewAction($image_id)
    {
        $hanzo = Hanzo::getInstance();
        $translator = $this->get('translator');
        $route = $this->get('request')->get('_route');
        $router = $this->get('router');

        $cache_id = array('product.image.bundle', $image_id);
        $products = $this->getCache($cache_id);

        if (empty($products)) {
            $product_ids = array();

            $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
            $locale = strtolower($hanzo->get('core.locale'));

            $product = ProductsQuery::create()
                ->useProductsImagesQuery()
                    ->filterById($image_id)
                ->endUse()
                ->filterByIsActive(TRUE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($hanzo->get('core.domain_id'))
                    ->filterByFromDate(array('max' => 'now'))
                    ->_or()
                    ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                    ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                    ->where(array('c1', 'c2'), 'AND')
                ->endUse()
                ->joinWithProductsImages()
                ->findOne()
            ;

            $product_ids[] = $product->getId();
            $products2category = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                ->filterBySku($product->getSku())
                ->endUse()
                ->findOne()
            ;

            $key = '_' . $locale . '_' . $products2category->getCategoriesId();
            $product_route = $router_keys[$key];

            $products[$product->getId()] = array(
                'id' => $product->getId(),
                'master' => $product->getSku(),
                'image' => $product->getProductsImagess()->getFirst()->getImage(),
                'url' => $router->generate($product_route, array(
                    'product_id' => $product->getId(),
                    'title' => Tools::stripText($product->getSku()),
                )),
                'out_of_stock' => true,
            );


            $result = ProductsQuery::create()
                ->useProductsImagesProductReferencesQuery()
                    ->filterByProductsImagesId($image_id)
                ->endUse()
                ->filterByIsActive(TRUE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($hanzo->get('core.domain_id'))
                    ->filterByFromDate(array('max' => 'now'))
                    ->_or()
                    ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                    ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                    ->where(array('c1', 'c2'), 'AND')
                ->endUse()
                ->joinWithProductsImages()
                ->find()
            ;

            foreach ($result as $product) {

                $products2category = ProductsToCategoriesQuery::create()
                    ->useProductsQuery()
                    ->filterBySku($product->getSku())
                    ->endUse()
                    ->findOne()
                ;

                $key = '_' . $locale . '_' . $products2category->getCategoriesId();
                $product_route = $router_keys[$key];

                $products[$product->getId()] = array(
                    'id' => $product->getId(),
                    'master' => $product->getSku(),
                    'image' => $product->getProductsImagess()->getFirst()->getImage(),
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getSku()),
                    )),
                    'out_of_stock' => true,
                );


                $product_ids[] = $product->getId();
            }

            foreach (ProductsDomainsPricesPeer::getProductsPrices($product_ids) as $i => $price) {
                $products[$i]['prices'] = $price;
            }

            $this->setCache($cache_id, $products);
        }

        foreach ($products as $id => $product) {
            $stock = $this->forward('WebServicesBundle:RestStock:check', array(
                'version' => 'v1',
                'master' => $product['master'],
                'id' => '',
            ));
            $stock = json_decode($stock->getContent());

            $options = array();
            if (isset($stock->data->products) && count($stock->data->products)) {
                foreach ($stock->data->products as $p) {
                    $options[$p->size] = $p->size;
                }
                $products[$id]['out_of_stock'] = false;
            }

            $products[$id]['options'] = $options;
        }

        $responce = $this->render('HanzoProductBundle:Bundle:view.html.twig', array(
            'page_type' => 'bundle',
            'products' => $products,
        ));
        return $responce;
    }


    public function customAction($set)
    {
        $hanzo = Hanzo::getInstance();
        $translator = $this->get('translator');
        $route = $this->get('request')->get('_route');
        $router = $this->get('router');

        $cache_id = array('product.bundle.custom', str_replace(',', '-', $set));
        $products = $this->getCache($cache_id);

        if (empty($products)) {
            $product_ids = explode(',', $set);
            $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
            $locale = strtolower($hanzo->get('core.locale'));

            $result = ProductsQuery::create()
                ->filterByIsActive(TRUE)
                ->filterById($product_ids)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($hanzo->get('core.domain_id'))
                    ->filterByFromDate(array('max' => 'now'))
                    ->_or()
                    ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                    ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                    ->where(array('c1', 'c2'), 'AND')
                ->endUse()
                ->joinWithProductsImages()
                ->find()
            ;

            foreach ($result as $product) {

                $products2category = ProductsToCategoriesQuery::create()
                    ->useProductsQuery()
                    ->filterBySku($product->getSku())
                    ->endUse()
                    ->findOne()
                ;

                $key = '_' . $locale . '_' . $products2category->getCategoriesId();
                $product_route = $router_keys[$key];

                $products[$product->getId()] = array(
                    'id' => $product->getId(),
                    'master' => $product->getSku(),
                    'image' => $product->getProductsImagess()->getFirst()->getImage(),
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getSku()),
                    )),
                    'out_of_stock' => true,
                );


                $product_ids[] = $product->getId();
            }

            foreach (ProductsDomainsPricesPeer::getProductsPrices($product_ids) as $i => $price) {
                $products[$i]['prices'] = $price;
            }

            $this->setCache($cache_id, $products);
        }

        foreach ($products as $id => $product) {
            $stock = $this->forward('WebServicesBundle:RestStock:check', array(
                'version' => 'v1',
                'master' => $product['master'],
                'id' => '',
            ));
            $stock = json_decode($stock->getContent());

            $options = array();
            if (isset($stock->data->products) && count($stock->data->products)) {
                foreach ($stock->data->products as $p) {
                    $options[$p->size] = $p->size;
                }
                $products[$id]['out_of_stock'] = false;
            }

            $products[$id]['options'] = $options;
        }

        $responce = $this->render('HanzoProductBundle:Bundle:view.html.twig', array(
            'page_type' => 'bundle',
            'products' => $products,
        ));

        return $responce;
    }
}
