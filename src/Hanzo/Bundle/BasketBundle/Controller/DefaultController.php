<?php

namespace Hanzo\Bundle\BasketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Stock,
    Hanzo\Core\CoreController;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsStockQuery;

class DefaultController extends CoreController
{
    public function addAction()
    {
        $this->get('twig')->addGlobal('page_type', 'basket');

        // product_id,master,size,color,quantity
        $request = $this->get('request');
        $quantity = $request->get('quantity', 1);

        $product_id = $request->get('product_id');
        if ($product_id) {
            $product = ProductsQuery::findOneById($product_id);
        }
        else {
            $master = $request->get('master');
            $size = $request->get('size');
            $color = $request->get('color');

            $product = ProductsQuery::create()
                ->filterByMaster($master)
                ->filterBySize($size)
                ->filterByColor($color)
                ->filterByIsOutOfStock(0)
                ->findOne()
            ;
        }

        // could not find matching product, throw 404 ?
        if (empty($product)) {
            $this->redirect($request->headers->get('referer'));
        }


        $stock = $this->get('stock')->check($product, $quantity);

        // $stock = Stock::check($product, $quantity);
        // if ($stock) {
        //     Stock::decrease($product, $quantity);
        // }

        return $this->render('BasketBundle:Default:add.html.twig', array('x' => $stock));
    }

    public function setAction($product_id, $qyantity)
    {
        return $this->response('set basket entry');
    }

    public function removeAction($product_id, $qyantity)
    {
        return $this->response('remove from basket');
    }

    public function updateAction()
    {
        return $this->response('update basket');
    }
}
