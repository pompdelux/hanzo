<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

class DefaultController extends CoreController
{
    public function viewAction($product_id)
    {
        $hanzo = Hanzo::getInstance();
        $translator = $this->get('translator');

        $cache_id = array('product', $product_id);
        $data = $this->getCache($cache_id);

        if (!$data) {
            $products = ProductsI18nQuery::create()
                ->joinWithProducts()
                ->filterByLocale($hanzo->get('core.locale'))
                ->useProductsQuery()
//                    ->filterByIsOutOfStock(FALSE)
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
                    ->useProductsWashingInstructionsQuery()
                        ->filterByLocale($hanzo->get('core.locale'))
                    ->endUse()
                    ->joinWithProductsWashingInstructions()
                    ->joinWithProductsImages()
                ->endUse()
                ->findById($product_id)
            ;

            $product = $products[0]->getProducts();

            // find all product images
            $images = array();
            $product_images = $product->getProductsImagess();
            foreach ($product_images as $image) {
                $images[$image->getId()] = array(
                    'id' => $image->getId(),
                    'name' => $image->getImage(),
                );
            }

            // set focus image
            if ($focus = $this->get('request')->get('focus', FALSE)) {
                if (isset($images[$focus])) {
                    $main_image = $images[$focus];
                    unset($images[$focus]);
                }
            }
            else {
                $main_image = array_shift($images);
            }

            // find the sizes and colors on stock
            $variants = ProductsQuery::create()
                ->select(array('Id', 'Size', 'Color'))
                ->distinct()
                ->findByMaster($product->getSku())
            ;

            $colors = $sizes = array();
            $product_ids = array();
            foreach ($variants as $v) {
                $product_ids[] = $v['Id'];
            }

            $stock = $this->get('stock');
            $stock->prime($product_ids);
            foreach ($variants as $v) {
                if ($stock->check($v['Id'])) {
                    $colors[$v['Color']] = $v['Color'];
                    $sizes[$v['Size']] = $v['Size'];
                }
            }

            $references = ProductsImagesProductReferencesQuery::create()
                ->useProductsImagesQuery()
                    ->filterByProductsId($product->getId())
                ->endUse()
                ->joinWithProductsImages()
                ->joinWithProducts()
                ->find()
            ;

            $route = $this->get('request')->get('_route');
            $router = $this->get('router');

            $images_references = array();
            foreach ($references as $ref) {
                $images_references[$ref->getProductsImagesId()]['references'][] = array(
                    'title' => $ref->getProducts()->getSku(),
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

            $washing = stripslashes($product->getProductsWashingInstructions()->getDescription());
            $washing = preg_replace($find, $replace, $washing);

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

        $this->get('twig')->addExtension(new \Twig_Extensions_Extension_Debug());

        $responce = $this->render('HanzoProductBundle:Default:view.html.twig', array(
            'page_type' => 'product',
            'product' => $data,
            'references' => $images_references,
        ));
        return $responce;
    }
}
