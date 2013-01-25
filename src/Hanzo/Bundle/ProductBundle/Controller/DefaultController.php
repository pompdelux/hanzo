<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Criteria;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\Stock;
use Hanzo\Core\CoreController;

use Hanzo\Model\CmsQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\ProductsStockPeer;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStock;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsWashingInstructions;
use Hanzo\Model\ProductsWashingInstructionsQuery;

class DefaultController extends CoreController
{
    public function viewAction($product_id)
    {
        $hanzo = Hanzo::getInstance();
        $translator = $this->get('translator');

        $router = $this->get('router');
        $route = $this->get('request')->get('_route');
        $focus = $this->get('request')->get('focus', FALSE);
        $cache_id = array('product', $product_id, $focus);
        $data = $this->getCache($cache_id);

        if (!$data) {
            $products = ProductsI18nQuery::create()
                ->joinWithProducts()
                ->filterByLocale($hanzo->get('core.locale'))
                ->useProductsQuery()
                    ->filterByIsActive(TRUE)
                    ->useProductsDomainsPricesQuery()
                        ->filterByDomainsId($hanzo->get('core.domain_id'))
                        ->filterByFromDate(array('max' => 'now'))
                        ->_or()
                        ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                        ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                        ->where(array('c1', 'c2'), 'AND')
                    ->endUse()
                    ->joinWithProductsDomainsPrices()
                    ->joinWithProductsImages()
                    ->joinWithProductsToCategories()
                ->endUse()
                ->findById($product_id)
            ;

            // if no product matched the query, throw a 404 exception
            if ($products->count() == 0) {
                throw $this->createNotFoundException($translator->trans('product.not.found'));
            }

            $product = $products[0]->getProducts();

            // find all product images
            $images = array();
            $product_images = $product->getProductsImagess();
            foreach ($product_images as $image) {
                $path_params = explode('_', explode('.', $image->getImage())[0]);
                $number = isset($path_params[3]) ? (int)$path_params[3] : 0;

                $images[$image->getId()] = array(
                    'id' => $image->getId(),
                    'name' => $image->getImage(),
                    'color' => $image->getColor(),
                    'type' => $image->getType(),
                    'number' => $number,
                );
            }

            // set focus image
            if ($focus && isset($images[$focus])) {
                $main_image = $images[$focus];
            }
            else {
                $main_image = array_shift($images);
            }

            $sorted = [];
            foreach ($images as $key => $data) {
                $sorted[$data['type'].$key] = $data;
            }
            ksort($sorted);
            $images = $sorted;

            $current_color = $main_image['color'];
            $current_type  = $main_image['type'];

            $colors = $sizes = array();
            $product_ids = array();

            // find the sizes and colors on stock
            if (!$product->getIsOutOfStock()) {
                $variants = ProductsQuery::create()->findByMaster($product->getSku());

                foreach ($variants as $v) {
                    $product_ids[] = $v->getId();
                }

                $stock = $this->get('stock');
                $stock->prime($product_ids);
                foreach ($variants as $v) {
                    if ($stock->check($v->getId())) {
                        $colors[$v->getColor()] = $v->getColor();
                        $sizes[$v->getSize()] = $v->getSize();
                    }
                }

                natcasesort($colors);
                natcasesort($sizes);
            }

            $references = ProductsImagesProductReferencesQuery::create()
                ->useProductsImagesQuery()
                    ->filterByProductsId($product->getId())
                ->endUse()
                ->joinWithProductsImages()
                ->joinWithProducts()
                ->find()
            ;

            $images_references = array();
            foreach ($references as $ref) {
                $images_references[$ref->getProductsImagesId()]['references'][] = array(
                    'title' => $ref->getProducts()->getSku(),
                    'image' => $ref->getProductsImages()->getImage(),
                    'url' => $router->generate($route, array(
                        'product_id' => $ref->getProductsId(),
                        'title'=> Tools::stripText($ref->getProducts()->getSku()),
                    ), TRUE),
                );
            }

            $translation_key = 'description.' . Tools::stripText($product->getSku(), '_', false);

            $find = '~(background|src)="(../|/)~';
            $replace = '$1="' . $hanzo->get('core.cdn');

            $description = $translator->trans($translation_key, array('%cdn%' => $hanzo->get('core.cdn')), 'products');
            $description = preg_replace($find, $replace, $description);

            $washing = '';
            $result = ProductsWashingInstructionsQuery::create()
                ->filterByLocale($hanzo->get('core.locale'))
                ->findOneByCode($product->getWashing())
            ;

            if ($result instanceof ProductsWashingInstructions) {
                $washing = stripslashes($result->getDescription());
                $washing = preg_replace($find, $replace, $washing);
            }
            //print_r(get_class_methods($product->getProductsToCategoriess()));
            //$cms_page = CmsQuery::create()->joinWithI18n($hanzo->get('core.locale'))->findOneById($product->getProductsToCategoriess()->getFirst()->getCategoriesId()); // Find this cms' parent. category
            //$parent_page = CmsQuery::create()->joinWithI18n($hanzo->get('core.locale'))->filterById($cms_page->getParentId())->findOne(); // 's parent

            $data = array(
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'title' => $product->getSku(),
                'description' => $description,
                'washing' => $washing,
                'main_image' => $main_image,
                'images' => $images,
                'prices' => array(),
                'out_of_stock' => $product->getIsOutOfStock(),
                'colors' => $colors,
                'sizes' => $sizes,
                'images_references' => $images_references
            );

            $this->setCache($cache_id, $data, 60);
        }

        // find and calculate prices
        $prices = ProductsDomainsPricesPeer::getProductsPrices(array($data['id']));
        $data['prices'] = array_shift($prices);

        $images_references = $data['images_references'];
        unset($data['images_references']);

        $this->get('twig')->addGlobal('page_type', 'product-'.$data['id']);
        $this->get('twig')->addGlobal('body_classes', 'body-product product-'.$data['id']);

        $this->setSharedMaxAge(300);
        $response = $this->render('ProductBundle:Default:view.html.twig', array(
            'page_type' => 'product',
            'product' => $data,
            'references' => $images_references,
            'cat_route' => str_replace('product', 'category', $route)
        ));
        return $response;
    }
}
