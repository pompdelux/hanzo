<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\BasketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\Stock,
    Hanzo\Core\CoreController
    ;

use Hanzo\Model\Products,
    Hanzo\Model\ProductsPeer,
    Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsStockQuery,
    Hanzo\Model\ProductsDomainsPricesPeer,
    Hanzo\Model\ProductsDomainsPricesQuery,
    Hanzo\Model\ProductsToCategoriesQuery,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\OrdersQuery,
    Hanzo\Model\OrdersLinesQuery
    ;

class DefaultController extends CoreController
{
    public function addAction()
    {
        $this->get('twig')->addGlobal('page_type', 'basket');
        $translator = $this->get('translator');

        // product_id,master,size,color,quantity
        $request = $this->get('request');
        $quantity = $request->get('quantity', 1);
        $product = ProductsPeer::findFromRequest($request);

        // could not find matching product, throw 404 ?
        if (!$product instanceof Products) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => ''
                ));
            }

            $this->redirect($request->headers->get('referer'));
        }

        $stock = $this->get('stock')->check($product, $quantity);

        if ($stock) {
            $date = $this->get('stock')->decrease($product, $quantity);
            $order = OrdersPeer::getCurrent();

            // hf@bellcom.dk, 23-jun-2012: check order state -->>
            if ( $order->getState() > Orders::STATE_PRE_PAYMENT )
            {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('order.state_pre_payment.locked', array(), 'checkout')
                ));
            }
            // <<-- hf@bellcom.dk, 23-jun-2012: check order state

            if ($order->isNew()) {
                $order->setLanguagesId(Hanzo::getInstance()->get('core.language_id'));
            }

            $order->setOrderLineQty($product, $quantity, false, $date);

            if ($order->validate()) {
                $order->save();

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => TRUE,
                        'message' => $translator->trans('product.added.to.cart', array('%product%' => $product)),
                        'data' => $this->miniBasketAction(TRUE),
                    ));
                }
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('product.out.of.stock', array('%product%' => $product)),
            ));
        }


        return $this->forward('BasketBundle:Default:view');
    }


    public function miniBasketAction($return = FALSE)
    {
        $order = OrdersPeer::getCurrent();
        $total = '('.$order->getTotalQuantity(true).') ' . Tools::moneyFormat( $order->getTotalPrice(true) );

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
                'message' => $this->get('translator')->trans('no.such.product.in.cart'),
            ));
        }

        return $this->response('remove from basket');
    }


    public function replaceItemAction()
    {
        // 0. sanetize request
        // 1. figure out whether or not the product is in the basket
        // 2. find out whether or not the new product is in stock
        // 2.1 if not kick request with a reply about it
        // 2.2. if there is delivery time on the product, get confirmation
        // 2.3. or just add the goddam product.
        // 3. remove the old product
        // 4. add the new

        $request = $this->get('request');
        $product_to_replace = $request->get('product_to_replace');

        $request_data = array(
            'quantity' => $request->get('quantity'),
            'master' => $request->get('master'),
            'size' => $request->get('size'),
            'color' => $request->get('color'),
        );

        $response = $this->forward('WebServicesBundle:RestStock:check', $request_data);
        $response = json_decode($response->getContent(), TRUE);

        if ($response['status'] && isset($response['data']['products']) && count($response['data']['products']) == 1) {
            $product = $response['data']['products'][0];

            // if the product is backordered, require a confirmation to continue
            if ($product['date'] && (FALSE === $request->get('confirmed', FALSE))) {
                return $this->json_response($response);
            }

            // ok, we proceed
            // first, nuke original product
            $this->forward('BasketBundle:Default:remove', array('product_id' => $product_to_replace, 'quantity' => 'all'));

            // then add new product to cart
            $response = $this->forward('BasketBundle:Default:add', $request_data);
            $response = json_decode($response->getContent(), TRUE);

            // we need the product!
            if ($response['status']) {
                $response['data'] = array();

                $product = ProductsPeer::findFromRequest($request);
                $prices = ProductsDomainsPricesPeer::getProductsPrices(array($product->getId()));
                $prices = array_shift($prices);

                foreach ($prices as $key => $price) {
                    $response['data'][$key.'_total'] = Tools::moneyFormat($price['price'] * $request->get('quantity'));
                    $response['data'][$key] = Tools::moneyFormat($price['price']);
                }
                $response['data']['basket'] = $this->miniBasketAction(TRUE);
                $response['data']['product_id'] = $product->getId();
            }
        }

        return $this->json_response($response);
    }


    public function updateAction()
    {
        return $this->response('update basket');
    }

    public function viewAction($embed = false, $orders_id = null)
    {
        if ($orders_id) {
            $order = OrdersQuery::create()->findOneById($orders_id);
        } else {
            $order = OrdersPeer::getCurrent();
        }

        $router = $this->get('router');
        $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
        $locale = strtolower(Hanzo::getInstance()->get('core.locale'));

        $products = array();
        $delivery_date = 0;

        // product lines- if any
        foreach ($order->getOrdersLiness() as $line) {
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);

            if ($line['type'] != 'product') {
                continue;
            }

            // find first products2category match
            $products2category = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                ->filterBySku($line['products_name'])
                ->endUse()
                ->findOne()
            ;

if (!$products2category) {
    Tools::log($locale.' -> '.$line['products_name']);
}
            // find matching router

            $line['expected_at'] = new \DateTime($line['expected_at']);

            $t = $line['expected_at']->getTimestamp();
            if (($t > 0) && ($t > time())) {
                $line['expected_at'] = $t;
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

            $master = ProductsQuery::create()->findOneBySku($line['products_name']);
            $line['url'] = $router->generate($product_route, array(
                'product_id' => $master->getId(),
                'title' => Tools::stripText($line['products_name']),
            ));

            $products[] = $line;
        }

        $template = 'BasketBundle:Default:view.html.twig';
        if ($embed) {
            $template = 'BasketBundle:Default:block.html.twig';
        }
        // else
        // {
        //     // Make sure that order state is building, but only if state has not passed state payment ok
        //     // If embed is set, we do not set the state again (e.g. when embedded on the checkout page)
        //     if ( ($order->getState() !== Orders::STATE_BUILDING) && ($order->getState() < Orders::STATE_PAYMENT_OK) )
        //     {
        //         $attributes = $order->getAttributes();
        //         if (empty($attributes->payment->transact)) {
        //             $order->setState( Orders::STATE_BUILDING );
        //             $order->save();
        //         }
        //     }
        // }

        return $this->render($template, array(
            'embedded' => $embed,
            'page_type' => 'basket',
            'products' => $products,
            'total' => $order->getTotalPrice(true),
            'delivery_date' => $delivery_date,
            'continue_shopping' =>  $router->generate('page_400'),
        ));
    }
}
