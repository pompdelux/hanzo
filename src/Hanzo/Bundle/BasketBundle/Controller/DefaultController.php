<?php

namespace Hanzo\Bundle\BasketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\Stock,
    Hanzo\Core\CoreController;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsStockQuery,
    Hanzo\Model\ProductsDomainsPricesQuery;

use Hanzo\Model\OrdersPeer,
    Hanzo\Model\OrdersQuery,
    Hanzo\Model\OrdersLinesQuery;

class DefaultController extends CoreController
{
    public function addAction()
    {
        $this->get('twig')->addGlobal('page_type', 'basket');
        $translator = $this->get('translator');

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
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
                ->endUse()
                ->findOne()
            ;
        }

        // could not find matching product, throw 404 ?
        if (empty($product)) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => ''
                ));
            }

            $this->redirect($request->headers->get('referer'));
        }

        $stock = $this->get('stock')->check($product, $quantity);

        // $stock = Stock::check($product, $quantity);
        // if ($stock) {
        //     Stock::decrease($product, $quantity);
        // }

        if ($stock) {
            // Stock::decrease($product, $quantity);
            $order = OrdersPeer::getCurrentOrder($this);

            if ($order->isNew()) {
                $order->setLanguagesId(Hanzo::getInstance()->get('core.language_id'));
            }

            $order->setOrderLineQty($product, $quantity);

            if ($order->validate()) {
                $order->save();

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => TRUE,
                        'message' => $translator->trans('"%product%" just added to your cart', array('%product%' => $product)),
                        'data' => $this->miniBasketAction(TRUE),
                    ));
                }
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('"%product%" is out of stock', array('%product%' => $product)),
            ));
        }


        return $this->forward('BasketBundle:Default:view');
    }


    public function miniBasketAction($return = FALSE)
    {
        $order = OrdersPeer::getCurrentOrder($this);
        $total = '('.$order->getTotalQuantity().') ' . $order->getTotalPrice();

        if ($return) {
            return $total;
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => '',
                'data' => $total,
            ));
        }

        return $this->response($total);
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

    public function viewAction()
    {
        return $this->response('show basket');
    }
}
