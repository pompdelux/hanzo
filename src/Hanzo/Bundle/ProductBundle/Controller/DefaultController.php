<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController;

use Hanzo\Model\ProductsDomainsPricesPeer,
    Hanzo\Model\ProductsI18nQuery,
    Hanzo\Model\ProductsStockPeer,
    Hanzo\Model\ProductsStock,
    Hanzo\Model\ProductsStockQuery
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
                    ->condition('c2', ProductsDomainsPricesPeer::FROM_DATE . ' >= NOW()')
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
        $data = array(
            'title' => $product->getTitle(),
            'description' => $product->getContent(),
            'washing' => $product->getProductsWashingInstructions()->getDescription(),
            'images' => $product->getProductsImagess()->toArray(),
            'prices' => array_shift($prices),
            'out_of_stock' => $product->getIsOutOfStock(),
        );

        $this->get('twig')->addExtension(new \Twig_Extensions_Extension_Debug());

        $responce = $this->render('HanzoProductBundle:Default:view.html.twig', array('product' => $data));
        return $responce;
    }
}
