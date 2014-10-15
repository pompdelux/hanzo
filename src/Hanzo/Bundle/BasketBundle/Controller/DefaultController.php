<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\BasketBundle\Controller;

use Hanzo\Bundle\BasketBundle\Event\BasketEvent;
use Hanzo\Bundle\BasketBundle\Service\InvalidSessionException;
use Hanzo\Bundle\BasketBundle\Service\OutOfStockException;
use Hanzo\Bundle\PaymentBundle\Methods\Pensio\InvalidOrderStateException;
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

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\BasketBundle
 */
class DefaultController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws PropelException
     * @throws \Exception
     */
    public function addAction(Request $request)
    {
        $this->get('twig')->addGlobal('page_type', 'basket');

        $order      = OrdersPeer::getCurrent();
        $session    = $request->getSession();
        $translator = $this->get('translator');

        // this is set in the dibs controller, but never unset - so we better do it here.
        if ($session->has('last_successful_order_id')) {
            $session->remove('last_successful_order_id');
        }

        // fraud detection
        $totalOrderQuantity = OrdersLinesQuery::create()
            ->select('total')
            ->filterByOrdersId($order->getId())
            ->filterByType('product')
            ->withColumn('SUM(quantity)', 'total')
            ->find()
            ->getFirst();

        // force login if customer has 20 or more items in the basket.
        if (($totalOrderQuantity >= 20) && !$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json_response([
                'force_login' => true,
                'message'     => $translator->trans('order.force.login.description', [], 'checkout'),
                'status'      => false,
            ]);
        }

        $fraudId = 'fraud_mail_send_'.$order->getId();
        if (($totalOrderQuantity >= 100) && !$session->has($fraudId)) {
            $mail = $this->get('mail_manager');
            $mail->setTo('hd@pompdelux.dk', 'Heinrich Dalby');
            $mail->setSubject("Måske en snyder på spil.");
            $mail->setBody("Hej,\n\nKig lige på ".$request->getLocale()." ordre: #".$order->getId()."\n\nDenne har ".$totalOrderQuantity." vare i kurven.\n\n-- \nmvh\nspambotten per\n");
            $mail->send();
            $session->set($fraudId, true);
        }

        // product_id,master,size,color,quantity
        $quantity = $request->request->get('quantity', 1);
        $product  = ProductsPeer::findFromRequest($request);

        // could not find matching product, throw 404 ?
        if ((!$product instanceof Products) || !$product->getColor() || !$product->getSize()) {
            if ($this->getFormat() == 'json') {
                return $this->json_response([
                    'message' => '',
                    'status'  => false,
                ]);
            }

            return $this->redirect($request->headers->get('referer'));
        }

        $productName = ProductsI18nQuery::create()
            ->select('Title')
            ->useProductsQuery()
                ->filterBySku($product->getMaster())
            ->endUse()
            ->filterByLocale($request->getLocale())
            ->findOne();

        $product->setLocale($request->getLocale());
        $product->setTitle($productName);

        $basket = $this->container->get('hanzo.basket');

        try {
            $basket->setOrder($order);
        } catch (InvalidOrderStateException $e) {
            return $this->json_response([
                'message' => $translator->trans('order.state_pre_payment.locked', [], 'checkout'),
                'status'  => false,
            ]);
        }

        try {
            $date = $basket->addProduct($product, $quantity);
        } catch (OutOfStockException $e) {
            if ($this->getFormat() == 'json') {
                return $this->json_response([
                    'message' => $translator->trans('product.out.of.stock', ['%product%' => $productName]),
                    'status'  => false,
                ]);
            }

            return $this->forward('BasketBundle:Default:view');
        } catch (InvalidSessionException $e) {
            return $this->resetOrderAndUser($e, $request);
        }

        $price = ProductsDomainsPricesPeer::getProductsPrices([$product->getId()]);
        $price = array_shift($price);
        $price = array_shift($price);

        $masterId = null;
        try {
            $masterId = $product->getProductsRelatedByMaster()->getId();
        } catch (\Exception $e) {
            Tools::log("Failed to get master::id for:\n".print_r($product->toArray(), 1)."------------------------------");
        }

        $latest = [
            'expected_at'  => '',
            'id'           => $product->getId(),
            'master_id'    => $masterId,
            'price'        => Tools::moneyFormat($price['price'] * $quantity),
            'single_price' => Tools::moneyFormat($price['price']),
        ];

        $t = new \DateTime($date);
        $t = $t->getTimestamp();
        if (($t > 0) && ($t > time())) {
            $latest['expected_at'] = $date;
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        $templateData = [
            'data'    => $this->miniBasketAction($request, true),
            'latest'  => $latest,
            'message' => $translator->trans('product.added.to.cart', ['%product%' => $product->getTitle().' '.$product->getSize().' '.$product->getColor()]),
            'status'  => true,
            'total'   => $order->getTotalQuantity(true),
        ];

        // Delete cached version in redis, used in the mega basket.
        $cacheId = [
            'BasketBundle:Default:megaBasket.html.twig',
            $session->getId(),
        ];

        $this->get('redis.main')->del($cacheId);

        if ($this->getFormat() == 'json') {
            return $this->json_response($templateData);
        }

        return $this->forward('BasketBundle:Default:view', $templateData);
    }


    /**
     * @param Request $request
     * @param bool    $return
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function miniBasketAction(Request $request, $return = false)
    {
        $order = OrdersPeer::getCurrent();
        $total = '(' . $order->getTotalQuantity(true) . ') ' . Tools::moneyFormat($order->getTotalPrice(true));

        if ($return) {
            return $total;
        }

        // some times edit order cookies are not "closed"
        if ($request->cookies->has('__ice') && $order->isNew()) {
            Tools::unsetEditCookies();
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        $warning = '';
        if ($this->getFormat() == 'json') {
            if (OrdersPeer::inEdit()) {
                $warning = Tools::getInEditWarning();
            }

            return $this->json_response([
                'data'    => [
                    'total'   => $total,
                    'warning' => $warning,
                ],
                'message' => '',
                'status'  => true,
            ]);
        }

        return $this->response('');
    }


    /**
     * @param int $product_id
     * @param int $quantity
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setAction($product_id, $quantity)
    {
        return $this->response('set basket entry');
    }

    /**
     * @param int $product_id
     * @param int $quantity
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws PropelException
     * @throws \Exception
     */
    public function removeAction($product_id, $quantity)
    {
        $order = OrdersPeer::getCurrent();

        $orderLines = $order->getOrdersLiness();
        $productFound = false;

        foreach ($orderLines as $k => $line) {
            if ($line->getProductsId() == $product_id) {
                $productFound = $line->getProducts();

                if ('all' == $quantity) {
                    unset($orderLines[$k]);
                } else {
                    $line->setQuantity($line->getQuantity() - $quantity);
                    $orderLines[$k] = $line;
                }

                break;
            }
        }

        if ($productFound) {
            $order->setOrdersLiness($orderLines);
            $order->setUpdatedAt(time());
            $order->save();

            $quantity = ('all' == $quantity)
                ? null
                : $quantity
            ;
            $this->container->get('event_dispatcher')->dispatch('basket.product.post_remove', new BasketEvent($order, $productFound, $quantity));

            $data = [
                'quantity' => $order->getTotalQuantity(true),
                'total'    => Tools::moneyFormat($order->getTotalPrice(true)),
            ];

            Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

            // Delete cached version in redis, used in the mega basket.
            $cacheId = [
                'BasketBundle:Default:megaBasket.html.twig',
                $this->getRequest()->getSession()->getId(),
            ];

            $this->get('redis.main')->del($cacheId);

            if ($this->getFormat() == 'json') {
                return $this->json_response([
                    'data'    => $data,
                    'message' => '',
                    'status'  => true,
                ]);
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'message' => $this->get('translator')->trans('no.such.product.in.cart'),
                'status'  => false,
            ]);
        }

        return $this->redirect($this->generateUrl('basket_view'));
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
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

        $request          = $this->get('request');
        $productToReplace = $request->request->get('product_to_replace');
        $product          = ProductsPeer::findFromRequest($request);
        $quantity         = $request->request->get('quantity');

        // could not find matching product, throw 404 ?
        if (!$product instanceof Products) {
            if ($this->getFormat() == 'json') {
                return $this->json_response([
                    'message' => '',
                    'status'  => false,
                ]);
            }
        }

        $stockService = $this->get('stock');
        $stock        = $stockService->check($product, $quantity);

        if ($stock) {
            $requestData = [
                'quantity' => $request->request->get('quantity'),
                'master'   => $request->request->get('master'),
                'size'     => $request->request->get('size'),
                'color'    => $request->request->get('color'),
            ];

            // if the product is backordered, require a confirmation to continue
            if ($stock instanceof \DateTime && (false === $request->request->get('confirmed', false))) {
                $requestData['date'] = $stock;

                return $this->json_response([
                    'message' => '',
                    'status'  => true,
                    'data' => [
                        'products' => [
                            ['date' => $stock->format('d/m-Y')]
                        ],
                    ],
                ]);
            }

            // ok, we proceed
            // first, nuke original product
            $this->forward('BasketBundle:Default:remove', ['product_id' => $productToReplace, 'quantity' => 'all']);

            // then add new product to cart
            $response = $this->forward('BasketBundle:Default:add', $requestData);
            $response = json_decode($response->getContent(), true);

            // we need the product!
            if ($response['status']) {
                $response['data'] = [];

                $prices  = ProductsDomainsPricesPeer::getProductsPrices([$product->getId()]);
                $prices  = array_shift($prices);

                foreach ($prices as $key => $price) {
                    $response['data'][$key.'_total'] = Tools::moneyFormat($price['price'] * $request->request->get('quantity'));
                    $response['data'][$key]          = Tools::moneyFormat($price['price']);
                }

                $response['data']['basket']     = $this->miniBasketAction($request, true);
                $response['data']['product_id'] = $product->getId();
            }

            return $this->json_response($response);
        }

        return $this->json_response([
            'message' => '',
            'status'  => false,
        ]);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction()
    {
        return $this->response('update basket');
    }


    /**
     * @param Request $request
     * @param bool    $embed
     * @param null    $orders_id
     * @param string  $template
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws PropelException
     * @throws \Exception
     */
    public function viewAction(Request $request, $embed = false, $orders_id = null, $template = 'BasketBundle:Default:view.html.twig')
    {
        // If this request is for the mega basket, check if we already has cached it.
        if ($template === 'BasketBundle:Default:megaBasket.html.twig') {
            $cacheId = [
                'BasketBundle:Default:megaBasket.html.twig',
                $request->getSession()->getId(),
            ];
            $html = $this->getCache($cacheId);
            if ($html) {
                return $html;
            }
        }

        if ($orders_id) {
            $order = OrdersQuery::create()->findOneById($orders_id);
        } else {
            $order = OrdersPeer::getCurrent();
        }

        $router       = $this->get('router');
        $routerKeys = include $this->container->getParameter('kernel.cache_dir').'/category_map.php';
        $locale       = strtolower(Hanzo::getInstance()->get('core.locale'));
        $translator   = $this->container->get('translator');

        $mode = $this->get('kernel')->getStoreMode();
        $cid  = ['category2group'];

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


        $products     = [];
        $deliveryDate = 0;

        // product lines- if any
        $c = new \Criteria();
        $c->addAscendingOrderByColumn(OrdersLinesPeer::PRODUCTS_NAME);
        $c->add(OrdersLinesPeer::TYPE, 'product');

        foreach ($order->getOrdersLiness($c) as $line) {
            $line->setProductsSize($line->getPostfixedSize($translator));
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);

            $line['url']          = '';
            $line['basket_image'] = '';

            $t = strtotime($line['expected_at']);
            if (($t > 0) && ($t > time())) {
                $line['expected_at'] = $t;
                if ($deliveryDate < $line['expected_at']) {
                    $deliveryDate = $line['expected_at'];
                }
            } else {
                $line['expected_at'] = null;
            }

            // we need the id and sku from the master record to generate image and url to product.
            $sql = "
                SELECT p.id, p.sku, p.primary_categories_id FROM products AS p
                WHERE p.sku = (
                    SELECT pp.master FROM products AS pp
                    WHERE pp.id = ".(int) $line['products_id']."
                )
            ";
            $master = \Propel::getConnection()
                ->query($sql)
                ->fetch(\PDO::FETCH_OBJ);

            if (empty($master)) {
                continue;
            }

            if ($master->primary_categories_id) {
                $categoryId = $master->primary_categories_id;
            } else {
                // find first products2category match
                $categoryId = ProductsToCategoriesQuery::create()
                    ->select('CategoriesId')
                    ->useProductsQuery()
                        ->useProductsi18nQuery()
                            ->filterByTitle($line['products_name'])
                            ->filterByLocale($this->getRequest()->getLocale())
                        ->endUse()
                    ->endUse()
                    ->findOne();
            }

            if ($categoryId) {
                $line['basket_image'] =
                    preg_replace('/[^a-z0-9]/i', '-', $master->sku) .
                    '_' .
                    preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $line['products_color'])) .
                    '_overview_01.jpg'
                ;

                // find matching router
                $key    = '_' . $locale . '_' . $categoryId;
                $group  = $category2group[$categoryId];

                $line['master'] = $master->sku;

                if ('consultant' == $mode) {
                    $line['url'] = $router->generate('product_info', ['product_id' => $master->id]);
                } else {
                    if (isset($routerKeys[$key])) {
                        $line['url'] = $router->generate($routerKeys[$key], [
                            'product_id' => $master->id,
                            'title'      => Tools::stripText($line['products_name']),
                        ]);
                    }
                }
            } else {
                $group = 0;
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

        if (!$embed) {
            // some times edit order cookies are not "closed"
            if ($request->cookies->has('__ice') && $order->isNew()) {
                Tools::unsetEditCookies();
            }

            Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);
        }

        $html = $this->render($template, [
            'continue_shopping' => $continueShopping,
            'delivery_date'     => $deliveryDate,
            'embedded'          => $embed,
            'page_type'         => 'basket',
            'products'          => $products,
            'total'             => $order->getTotalPrice(true),
        ]);

        // If this request is for the mega basket, be sure to cache it.
        if ($template === 'BasketBundle:Default:megaBasket.html.twig') {
            $this->setCache($cacheId, $html, 5);
        }

        return $html;
    }

    /**
     * @param \Exception $e
     * @param Request    $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function resetOrderAndUser($e, Request $request)
    {
        // if the session is expired, we issue the user a new session and send him on his way
        $session = $request->getSession();
        if (false !== strpos($e->getMessage(), "Integrity constraint violation: 1062 Duplicate entry '".$session->getId()."' for key")) {
            $session->migrate();
            $session->save();

            Tools::setCookie('basket', '(0) '.Tools::moneyFormat(0.00), 0, false);

            return $this->json_response([
                'data'    => [
                    'location' => $this->generateUrl('_homepage')
                ],
                'message' => 'session.died',
                'status'  => false,
            ]);
        }
    }
}
