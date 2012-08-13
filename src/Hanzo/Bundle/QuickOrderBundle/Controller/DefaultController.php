<?php

namespace Hanzo\Bundle\QuickOrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsDomainsPricesQuery,
    Hanzo\Model\OrdersPeer
;
use \PropelCollection;
use Hanzo\Core\CoreController;

class DefaultController extends CoreController
{

    public function indexAction()
    {
    	$order = OrdersPeer::getCurrent();
    	$products = array();
    	foreach ($order->getOrdersLiness() as $line) {

            $basket_image =
                preg_replace('/[^a-z0-9]/i', '', $line->getProductsName()) .
                '_basket_' .
                preg_replace('/[^a-z0-9]/i', '', $line->getProductsColor()) .
                '.jpg'
                ;

    		$products[] = array(
    			'sku' => $line->getProductsSku(),
    			'quantity' => $line->getQuantity(),
                'basket_image' => $basket_image,
                'name' => $line->getProductsName(),
    		);
    	}
        return $this->render('QuickOrderBundle:Default:index.html.twig',
         	array(
         		'page_type' => 'quickorder',
         		'products' => $products
         	)
        );
    }

    public function getSkuAction()
    {
        $request = $this->get('request');
        $max_rows = $request->get('max_rows', 12);
        $name = $request->get('name');
        $callback = $request->get('callback');

    	$products = ProductsQuery::create()
            ->where('products.MASTER IS NULL')
            ->filterByIsOutOfStock(FALSE)
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
            ->endUse()
            ->filterBySku(''.$name.'%')
            ->groupBySku()
            ->orderBySku()
            ->limit($max_rows)
            ->find()
        ;

        if(!$products instanceof PropelCollection) {
    		if ($this->getFormat() == 'json') {
	            return $this->json_response(array(
	            	'status' => false,
	            	'message' => $this->get('translator')->trans('quickorder.no.products.found', array(), 'quickorder')
	            ));
	        }
	    }

        $result = array();
        foreach ($products as $product) {
        	$result[] = $product->getSku();
        }

		if ($this->getFormat() == 'json') {
            return $this->json_response(array(
            	'status' => true,
            	'message' => $this->get('translator')->trans('quickorder.products.found', array(), 'quickorder'),
            	'data' => $result
            ));
        }
    }
}
