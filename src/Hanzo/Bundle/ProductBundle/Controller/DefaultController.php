<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController;

use Hanzo\Model\ProductsDomainsPricesPeer,
    Hanzo\Model\ProductsI18nQuery,
    Hanzo\Model\ProductsStockPeer,
    Hanzo\Model\ProductsStock,
    Hanzo\Model\ProductsStockQuery,
    Hanzo\Model\ProductsImagesProductReferencesQuery
;

class DefaultController extends CoreController
{
    public function viewAction($product_id)
    {
        $hanzo = $this->get('hanzo');

        $products = ProductsI18nQuery::create()
            ->joinWithProducts()
            ->filterByLocale($hanzo->get('core.locale'))
            ->useProductsQuery()
                ->filterByIsOutOfStock(FALSE)
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
        //error_log(print_r(get_class_methods($product),1));

        $prices = ProductsDomainsPricesPeer::getProductsPrices($this, array($product->getId()));

        $images = array();
        $product_images = $product->getProductsImagess();
        foreach ($product_images as $image) {
            $images[$image->getId()] = array(
                'id' => $image->getId(),
                'name' => $image->getImage(),
            );
        }

        if ($focus = $this->get('request')->get('focus', FALSE)) {
            $main_image = $images[$focus];
            unset($images[$focus]);
        }
        else {
            $main_image = array_shift($images);
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
                'title' => $ref->getProducts()->getMaster(),
                'url' => $router->generate($route, array(
                    'product_id' => $ref->getProductsId(),
                    'title'=> $this->stripText($ref->getProducts()->getMaster()),
                ), TRUE),
            );
        }
#bc_log($images_references);

        $data = array(
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'description' => $product->getContent(),
            'washing' => stripslashes($product->getProductsWashingInstructions()->getDescription()),
            'main_image' => $main_image,
            'images' => $images,
            'prices' => array_shift($prices),
            'out_of_stock' => $product->getIsOutOfStock(),
            'references' => $images_references,
        );

        $this->get('twig')->addExtension(new \Twig_Extensions_Extension_Debug());

        $responce = $this->render('HanzoProductBundle:Default:view.html.twig', array(
            'page_type' => 'product',
            'product' => $data
        ));
        return $responce;
    }
}
