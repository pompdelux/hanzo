<?php

namespace Hanzo\Bundle\BasketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\Stock,
    Hanzo\Core\CoreController;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsStockQuery,
    Hanzo\Model\ProductsDomainsPricesQuery,
    Hanzo\Model\ProductsToCategoriesQuery;

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
            $order = OrdersPeer::getCurrent();

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
        $order = OrdersPeer::getCurrent();
        $total = '('.$order->getTotalQuantity().') ' . Tools::moneyFormat( $order->getTotalPrice() );

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


    public function setAction($product_id, $quantity)
    {
        return $this->response('set basket entry');
    }

    public function removeAction($product_id, $quantity)
    {
        $order = OrdersPeer::getCurrent();
        $order_lines = $order->getOrdersLiness();
        $product_found = FALSE;

        foreach ($order_lines as $k => $line) {
            if ($line->getProductsId() == $product_id) {
                $product_found = TRUE;

                if ($quantity == 'all') {
                    unset($order_lines[$k]);
                }
                else {
                    $line->setQuantity($line->getQuantity() - $quantity);
                    $order_lines[$k] = $line;
                }

                break;
            }
        }

        if ($product_found) {
            $order->setOrdersLiness($order_lines);
            $order->save();

            $data = array(
                'total' => Tools::moneyFormat($order->getTotalPrice()),
                'quantity' => $order->getTotalQuantity()
            );

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => '',
                    'data' => $data,
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('No such product in your cart.'),
            ));
        }

        return $this->response('remove from basket');
    }

    public function updateAction()
    {
        return $this->response('update basket');
    }

    public function viewAction()
    {
        $order = OrdersPeer::getCurrent();

        $router = $this->get('router');
        $router_keys = include __DIR__ . '/../Resources/config/category_map.php';
        $locale = strtolower(Hanzo::getInstance()->get('core.locale'));

        $products = array();
        $delivery_date = 0;

        // product lines- if any
        foreach ($order->getOrdersLiness() as $line) {
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);


            // find first products2category match
            $products2category = ProductsToCategoriesQuery::create()->findOneByProductsId($line['products_id']);
            // find matching router

            if ($line['expected_at']->getTimestamp() > 0) {
                $line['expected_at'] = $line['expected_at']->getTimestamp();
                if ($delivery_date < $line['expected_at']) {
                    $delivery_date = $line['expected_at'];
                }
            }
            else {
                $line['expected_at'] = NULL;
            }

            $line['basket_image'] =
                preg_replace('/[^a-z0-9]/i', '', $line['products_name']) .
                '_basket_' .
                preg_replace('/[^a-z0-9]/i', '', $line['products_color']) .
                '.jpg'
            ;

            $product_route = '';
            $key = '_' . $locale . '_' . $products2category->getCategoriesId();
            if (isset($router_keys[$key])) {
                $product_route = $router_keys[$key];
            }

            $line['url'] = $router->generate($product_route, array(
                'product_id' => $line['products_id'],
                'title' => Tools::stripText($line['products_name']),
            ));

            $products[] = $line;
        }

        return $this->render('BasketBundle:Default:view.html.twig', array(
            'page_type' => 'basket',
            'products' => $products,
            'total' => $order->getTotalPrice(),
            'delivery_date' => $delivery_date,
            'continue_shopping' =>  $router->generate('page_400_' . $locale),
        ));
    }
}
