<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Criteria;

use Hanzo\Model\ProductsImagesPeer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\CmsQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\ProductsQuery;
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
        $image_ids = array();

        $c = new \Criteria();
        $c->addAscendingOrderByColumn(ProductsImagesPeer::COLOR);
        $c->addDescendingOrderByColumn(ProductsImagesPeer::TYPE);
        $c->addAscendingOrderByColumn(ProductsImagesPeer::IMAGE);
        $product_images = $product->getProductsImagess($c);

        foreach ($product_images as $image) {
            $path_params = explode('_', explode('.', $image->getImage())[0]);
            $number = isset($path_params[3]) ? (int)$path_params[3] : 0;
            $image_ids[] = $image->getId(); // Used for references

            $images[$image->getId()] = array(
                'id' => $image->getId(),
                'name' => $image->getImage(),
                'color' => $image->getColor(),
                'type' => $image->getType(),
                'number' => $number,
            );
        }

        // Use to kep an array of all images with keys. array_shift broke the keys.
        $all_images = $images;

        // set focus image
        if ($focus && isset($images[$focus])) {
            $main_image = $images[$focus];
        } else {
            $main_image = reset($images);
        }

        $sorted_images = [];
        foreach ($images as $key => $data) {
            $s = $data['type'];
            if ('set' === $s) {
                $s = 'aaa'.$s;
            }
            $sorted_images[$data['color'].$s.$key] = $data;
        }
        ksort($sorted_images);

        $all_colors = $colors = $sizes = array();
        $product_ids = array();
        $variants = ProductsQuery::create()->findByMaster($product->getSku());

        $sizes = [];

        // All colors are used for colorbuttons
        foreach ($variants as $v) {
            $all_colors[$v->getColor()] = $v->getColor();
            $sizes[$v->getSize()] = [
                'label' => $v->getPostfixedSize($translator),
                'value' => $v->getSize(),
                'in_stock' => false,
            ];
        }

        $colors = $all_colors;
        // find the sizes and colors on stock
        if (!$product->getIsOutOfStock()) {
            foreach ($variants as $v) {
                $product_ids[] = $v->getId();
            }

            $stock = $this->get('stock');
            $stock->prime($product_ids);
            foreach ($variants as $v) {
                if ($stock->check($v->getId())) {
                    $sizes[$v->getSize()]['in_stock'] = true;
                }
            }

            natcasesort($colors);
            uksort($sizes, function ($a, $b) {
                return (int) $a - (int) $b;
            });
        }

        $references = ProductsImagesProductReferencesQuery::create()
            ->withColumn('products_images.ID')
            ->withColumn('products_images.COLOR')
            ->withColumn('products_images.IMAGE')
            ->filterByProductsImagesId($image_ids)
            ->joinWithProducts()
            ->useProductsQuery()
                ->joinWithProductsImages()
                ->useProductsImagesQuery()
                    ->filterByType('overview')
                    ->where('products_images.COLOR = products_images_product_references.COLOR')
                ->endUse()
            ->endUse()
            ->find()
        ;

        $images_references = array();
        foreach ($references as $ref) {
            $sku = $ref->getProducts()->getTitle();
            $images_references[$ref->getProductsImagesId()]['references'][$ref->getProductsId()] = array(
                'title' => $sku,
                'color' => $ref->getVirtualColumn('products_imagesCOLOR'),
                'image' => $ref->getVirtualColumn('products_imagesIMAGE'),
                'url' => $router->generate($route, array(
                    'product_id' => $ref->getProductsId(),
                    'title'=> Tools::stripText($sku),
                    'focus'=> $ref->getVirtualColumn('products_imagesID'),
                ), TRUE),
            );
        }

        foreach ($images_references as $image_id => &$references) {
            // If there are any references to this image,
            // Add an overview of the current product at the top of the array.
            if (count($references['references']) > 0) {
                array_unshift($references['references'], array(
                    'title' => $product->getSku(),
                    'color' => '',
                    'image' => preg_replace('/_set_[0-9]+/', '_overview_01', $all_images[$image_id]['name']),
                    'url' => $router->generate($route, array(
                        'product_id' => $product->getId(),
                        'title'=> Tools::stripText($product->getSku()),
                    ), TRUE),
                ));
            }
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

        $data = array(
            'id' => $product->getId(),
            'sku' => $product->getSku(),
            'title' => $product->getTitle(),
            'description' => $description,
            'washing' => $washing,
            'main_image' => $main_image,
            'images' => $sorted_images,
            'prices' => [],
            'out_of_stock' => $product->getIsOutOfStock(),
            'colors' => $colors,
            'all_colors' => $all_colors,
            'sizes' => $sizes,
            'images_references' => $images_references,
            'has_video' => (bool) $product->getHasVideo()
        );


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
            'browser_title' => $product->getTitle(),
            '_route' => $route
        ));
        return $response;
    }
}
