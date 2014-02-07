<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\BasketBundle\Controller;

use Hanzo\Model\ProductsI18nQuery;
use Symfony\Component\HttpFoundation\Request;

use PropelException;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Products;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\CategoriesQuery;

class DefaultController extends CoreController
{
    public function addAction(Request $request)
    {
        $this->get('twig')->addGlobal('page_type', 'basket');

        $session = $request->getSession();

        // this is set in the dibs controller, but never unset - so we better do it here.
        if ($session->has('last_successful_order_id')) {
            $session->remove('last_successful_order_id');
        }

        // product_id,master,size,color,quantity
        $translator = $this->get('translator');
        $quantity   = $request->request->get('quantity', 1);
        $product    = ProductsPeer::findFromRequest($request);

        // could not find matching product, throw 404 ?
        if (!$product instanceof Products) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'message' => '',
                    'status'  => false,
                ));
            }

            return $this->redirect($request->headers->get('referer'));
        }

        $product_name = ProductsI18nQuery::create()
            ->select('Title')
            ->useProductsQuery()
                ->filterBySku($product->getMaster())
            ->endUse()
            ->filterByLocale($request->getLocale())
            ->findOne()
        ;

        $product->setLocale($request->getLocale());
        $product->setTitle($product_name);

        $stock_service = $this->get('stock');
        $stock         = $stock_service->check($product, $quantity);

        if (false === $stock) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'message' => $translator->trans('product.out.of.stock', ['%product%' => $product_name]),
                    'status'  => false,
                ));
            }

            return $this->forward('BasketBundle:Default:view');
        }

        $date  = $stock_service->decrease($product, $quantity);
        $order = OrdersPeer::getCurrent();

        if ($order->getState() >= Orders::STATE_PRE_PAYMENT) {
            return $this->json_response(array(
                'message' => $translator->trans('order.state_pre_payment.locked', array(), 'checkout'),
                'status'  => false,
            ));
        }


        // fraud detection
        $total_order_quantity = OrdersLinesQuery::create()
            ->select('total')
            ->filterByOrdersId($order->getId())
            ->filterByType('product')
            ->withColumn('SUM(quantity)', 'total')
            ->find()
            ->getFirst()
        ;

        // force login if customer has 20 or more items in the basket.
        if (($total_order_quantity >= 20) && !$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json_response(array(
                'force_login' => true,
                'message'     => $translator->trans('order.force.login.description', array(), 'checkout'),
                'status'      => false,
            ));
        }

        $fraud_id = 'fraud_mail_send_'.$order->getId();
        if (($total_order_quantity >= 100) && !$session->has($fraud_id)) {
            $mail = $this->get('mail_manager');
            $mail->setTo('hd@pompdelux.dk', 'Heinrich Dalby');
            $mail->setSubject("Måske en snyder på spil.");
            $mail->setBody("Hej,\n\nKig lige på ".$this->getRequest()->getLocale()." ordre: #".$order->getId()."\n\nDenne har ".$total_order_quantity." vare i kurven.\n\n-- \nmvh\nspambotten per\n");
            $mail->send();
            $session->set($fraud_id, true);
        }


        // we need to figure out why some information seems to hang in the session where it shouldn't
        // this is one of the things that could be wrong, so we test it and logs any findings.
        if ($session->has('last_successful_order_id')) {
            if ($session->get('last_successful_order_id') == $order->getId()) {
                // this should not be possible, but the test is here to see if there is some issues we need to address...
                $message =
                    "We should not be here !!!\n".
                    'Session: '.print_r($session->all(), 1)."\n".
                    'Order..: '.print_r($order->toArray(), 1).
                    "\n- - - - - -\n"
                ;
                Tools::log($message);
            }
        }

        $order->setOrderLineQty($product, $quantity, false, $date);
        $order->setUpdatedAt(time());

        try {
            $order->save();
        } catch(PropelException $e) {
            return $this->resetOrderAndUser($e, $request);
        }

        $price = ProductsDomainsPricesPeer::getProductsPrices(array($product->getId()));
        $price = array_shift($price);
        $price = array_shift($price);

        $latest = array(
            'expected_at'  => '',
            'id'           => $product->getId(),
            'master_id'    => $product->getProductsRelatedByMaster()->getId(),
            'price'        => Tools::moneyFormat($price['price'] * $quantity),
            'single_price' => Tools::moneyFormat($price['price']),
        );

        $t = new \DateTime($date);
        $t = $t->getTimestamp();
        if (($t > 0) && ($t > time())) {
            $latest['expected_at'] = $date;
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        $template_data = [
            'data'    => $this->miniBasketAction(TRUE),
            'latest'  => $latest,
            'message' => $translator->trans('product.added.to.cart', array('%product%' => $product->getTitle().' '.$product->getSize().' '.$product->getColor())),
            'status'  => TRUE,
            'total'   => $order->getTotalQuantity(true),
        ];

        if ($this->getFormat() == 'json') {
            return $this->json_response($template_data);
        }

        return $this->forward('BasketBundle:Default:view', $template_data);
    }


    public function miniBasketAction($return = false)
    {
        $order = OrdersPeer::getCurrent();
        $total = '('.$order->getTotalQuantity(true).') ' . Tools::moneyFormat( $order->getTotalPrice(true) );

        if ($return) {
            return $total;
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        $warning = '';
        if ($this->getFormat() == 'json') {
            if (OrdersPeer::inEdit()) {
                $warning = Tools::getInEditWarning();
            }

            return $this->json_response(array(
                'data'    => array(
                    'total'   => $total,
                    'warning' => $warning,
                ),
                'message' => '',
                'status'  => TRUE,
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
        $product_found = false;

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
            }
        }

        if ($product_found) {
            $order->setOrdersLiness($order_lines);
            $order->setUpdatedAt(time());
            $order->save();

            $data = array(
                'quantity' => $order->getTotalQuantity(true),
                'total'    => Tools::moneyFormat($order->getTotalPrice(true)),
            );

            Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'data'    => $data,
                    'message' => '',
                    'status'  => TRUE,
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'message' => $this->get('translator')->trans('no.such.product.in.cart'),
                'status'  => false,
            ));
        }

        return $this->redirect($this->generateUrl('basket_view'));
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

        $request            = $this->get('request');
        $product_to_replace = $request->request->get('product_to_replace');

        $request_data = array(
            'quantity' => $request->request->get('quantity'),
            'master'   => $request->request->get('master'),
            'size'     => $request->request->get('size'),
            'color'    => $request->request->get('color'),
        );

        $response = $this->forward('WebServicesBundle:RestStock:check', $request_data);
        $response = json_decode($response->getContent(), TRUE);

        if ($response['status'] && isset($response['data']['products']) && count($response['data']['products']) == 1) {
            $product = $response['data']['products'][0];

            // if the product is backordered, require a confirmation to continue
            if ($product['date'] && (false === $request->request->get('confirmed', false))) {
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
                $prices  = ProductsDomainsPricesPeer::getProductsPrices(array($product->getId()));
                $prices  = array_shift($prices);

                foreach ($prices as $key => $price) {
                    $response['data'][$key.'_total'] = Tools::moneyFormat($price['price'] * $request->request->get('quantity'));
                    $response['data'][$key]          = Tools::moneyFormat($price['price']);
                }
                $response['data']['basket']     = $this->miniBasketAction(TRUE);
                $response['data']['product_id'] = $product->getId();
            }
        }

        return $this->json_response($response);
    }


    public function updateAction()
    {
        return $this->response('update basket');
    }


    public function viewAction($embed = false, $orders_id = null, $template = 'BasketBundle:Default:view.html.twig')
    {
        $cache_id = explode('_', $this->get('request')->get('_route'));
        $cache_id[] = $this->getRequest()->getSession()->getId();

        // If this request is for the mega basket, check if we already has cached it.
        if (rawurldecode($this->getRequest()->getPathInfo()) === $this->container->getParameter('fragment.path')) {
            $html = $this->getCache($cache_id);
            if ($html) {
                return new Response($html);
            }
        }

        if ($orders_id) {
            $order = OrdersQuery::create()->findOneById($orders_id);
        } else {
            $order = OrdersPeer::getCurrent();
        }

        $router      = $this->get('router');
        $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
        $locale      = strtolower(Hanzo::getInstance()->get('core.locale'));

        $mode = $this->get('kernel')->getStoreMode();
        $cid  = array('category2group');

        $category2group = $this->getCache($cid);
        if (empty($category2group)) {
            $result = CategoriesQuery::create()
                ->filterByContext(null, \Criteria::ISNOTNULL)
                ->orderByContext()
                ->find();
            foreach ($result as $category) {
                list($group, ) = explode('_', $category->getContext());
                $category2group[$category->getId()] = 'product.group.'.strtolower($group);
            }
            $this->setCache($cid, $category2group);
        }


        $products = array();
        $delivery_date = 0;

        // product lines- if any
        $c = new \Criteria();
        $c->addAscendingOrderByColumn(OrdersLinesPeer::PRODUCTS_NAME);
        foreach ($order->getOrdersLiness($c) as $line) {
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);

            if ($line['type'] != 'product') {
                continue;
            }

            $t = strtotime($line['expected_at']);
            if (($t > 0) && ($t > time())) {
                $line['expected_at'] = $t;
                if ($delivery_date < $line['expected_at']) {
                    $delivery_date = $line['expected_at'];
                }
            } else {
                $line['expected_at'] = NULL;
            }

            // find first products2category match
            $category_id = ProductsToCategoriesQuery::create()
                ->select('CategoriesId')
                ->useProductsQuery()
                    ->useProductsi18nQuery()
                        ->filterByTitle($line['products_name'])
                        ->filterByLocale($this->getRequest()->getLocale())
                    ->endUse()
                ->endUse()
                ->findOne();

            if ($category_id) {
                // we need the id and sku from the master record to generate image and url to product.
                $sql = "
                    SELECT p.id, p.sku FROM products AS p
                    WHERE p.sku = (
                        SELECT pp.master FROM products AS pp
                        WHERE pp.id = ".$line['products_id']."
                    )
                ";
                $master = \Propel::getConnection()
                    ->query($sql)
                    ->fetch(\PDO::FETCH_OBJ);

                $line['basket_image'] =
                    preg_replace('/[^a-z0-9]/i', '-', $master->sku) .
                    '_' .
                    preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $line['products_color'])) .
                    '_overview_01.jpg'
                ;

                // find matching router
                $key    = '_' . $locale . '_' . $category_id;
                $group  = $category2group[$category_id];

                $line['master'] = $master->sku;

                if ('consultant' == $mode) {
                    $line['url'] = $router->generate('product_info', array('product_id' => $master->id));
                } else {
                    if (isset($router_keys[$key])) {
                        $line['url'] = $router->generate($router_keys[$key], array(
                            'product_id' => $master->id,
                            'title'      => Tools::stripText($line['products_name']),
                        ));
                    }
                }
            } else {
                $group                = 0;
                $line['basket_image'] = '';
                $line['url']          = '';
            }

            $products[$group][] = $line;
        }

        ksort($products);

        if ($embed) {
            $template = 'BasketBundle:Default:block.html.twig';
        }

        $continueShopping = $this->generateUrl('_homepage', ['_locale' => Hanzo::getInstance()->get('core.locale')]);

        $hanzo = Hanzo::getInstance();
        $domainKey = $hanzo->get('core.domain_key');
        if (strpos($domainKey, 'Sales') !== false) {
            $continueShopping = $router->generate('QuickOrderBundle_homepage');
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        $html = $this->render($template, array(
            'continue_shopping' => $continueShopping,
            'delivery_date'     => $delivery_date,
            'embedded'          => $embed,
            'page_type'         => 'basket',
            'products'          => $products,
            'total'             => $order->getTotalPrice(true),
        ));
        // If this request is for the mega basket, be sure to cache it.
        if (rawurldecode($this->getRequest()->getPathInfo()) === $this->container->getParameter('fragment.path')) {
            $this->setCache($cache_id, $html, 5);
        }
        return $html;
    }


    private function resetOrderAndUser($e, Request $request)
    {
        // if the session is expired, we issue the user a new session and send him on his way
        $session = $request->getSession();
        if (false !== strpos($e->getMessage(), "Integrity constraint violation: 1062 Duplicate entry '".$session->getId()."' for key")) {
            $session->migrate();
            $session->save();

            Tools::setCookie('basket', '(0) '.Tools::moneyFormat(0.00), 0, false);
            return $this->json_response(array(
                'data'    => [
                    'location' => $this->generateUrl('_homepage')
                ],
                'message' => 'session.died',
                'status'  => false,
            ));
        }
    }
}
