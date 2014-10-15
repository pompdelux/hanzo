<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ShippingMethods;
use Hanzo\Model\CouponsQuery;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

use Exception;
use Propel;
use BasePeer;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\CheckoutBundle
 */
class DefaultController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws Exception
     * @throws \PropelException
     */
    public function indexAction(Request $request)
    {
        $order = OrdersPeer::getCurrent(true);

        if ( ($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        // trigger event, handles discounts and other stuff.
        $this->get('event_dispatcher')->dispatch('order.summery.finalize', new FilterOrderEvent($order));

        if ($order->isHostessOrder() && ($order->getTotalPrice() < 0)) {
            $request->getFlashBag()->add('notice', $this->get('translator')->trans('order.amount.to.low', [], 'checkout'));

            return $this->redirect($this->generateUrl('basket_view'));
        }

        $order->setUpdatedAt(time());
        $order->save();

        return $this->render('CheckoutBundle:Default:flow.html.twig', ['page_type' => 'checkout']);
    }


    /**
     * confirmAction
     *
     * @return Response
     **/
    public function confirmAction()
    {
        return $this->render('CheckoutBundle:Default:confirm.html.twig', [
            'skip_my_account' => true,
        ]);
    }


    /**
     * summeryAction
     *
     * @return Response
     **/
    public function summeryAction()
    {
        $invalid_order_message = '';

        // we only want the master data here, no slaves thank you...
        Propel::setForceMasterConnection(true);

        $order = OrdersPeer::getCurrent(true);
        $hanzo = Hanzo::getInstance();
        $domain_key = $hanzo->get('core.domain_key');

        // first we finalize the order, aka. setting misc order attributes and updating lines ect.

        // order product lines
        // - we need to set original_price and unit
        $products = OrdersLinesQuery::create()
            ->joinWithProducts()
            ->filterByType('product')
            ->findByOrdersId($order->getId())
        ;

        $product_ids = array();
        $product_units = array();
        foreach ($products as $product) {
            $product_ids[] = $product->getProductsId();
            $product_units[$product->getProductsId()] = $product->getProducts()->getUnit();
        }

        $product_prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);

        foreach ($products as $product) {
            $product->setOriginalPrice($product_prices[$product->getProductsId()]['normal']['price']);
            $product->setUnit('Stk.');
            $product->save();
        }

        if (!$order->getDeliveryMethod()) {
            $shippingApi = $this->get('shipping.shippingapi');
            $shipping_methods = $shippingApi->getMethods();

            if (('DKK' == $order->getCurrencyCode()) && $order->getDeliveryCompanyName()) {
                $order->setDeliveryMethod(11);
            } else {
                $firstShippingMethod = array_shift($shipping_methods);
                $order->setDeliveryMethod( $firstShippingMethod->getExternalId() );
            }
        }

        $attributes = $order->getAttributes();

        // must be set, so er ensure that they are.

        if (!$order->getCurrencyCode()) {
            $order->setCurrencyCode($hanzo->get('core.currency'));
        }

        if (empty($attributes->global->HomePartyId)) {
            $key = str_replace('Sales', '', $domain_key);
            $order->setAttribute('HomePartyId', 'global', 'WEB ' . $key);
        }

        if (empty($attributes->global->SalesResponsible)) {
            $key = str_replace('Sales', '', $domain_key);
            $order->setAttribute('SalesResponsible', 'global', 'WEB ' . $key);
        }

        if (empty($attributes->global->domain_key)) {
            $order->setAttribute('domain_key', 'global', $domain_key);
        }

        // sometimes free shipping is ignored, we try to handle errors here
        $free_limit = $hanzo->get('shipping.free_shipping', 0);
        if ($free_limit > 0) {
            $total = $order->getTotalPrice(true);

            if ($free_limit && ($total > $free_limit)) {
                foreach ($order->getOrderLineShipping() as $line) {
                    if ($line->getPrice() > 0) {
                        $line->setPrice(0.00);
                        $line->save();
                    }
                }
            }
        }

        // make sure to remove double discount
        if (isset($attributes->purchase) &&
            isset($attributes->purchase->type) &&
            ($attributes->purchase->type !== 'private')
        ) {
            $order->removeDiscountLine('discount.private');
        }

        // make sure to remove double discount
        if (isset($attributes->coupon) &&
            !empty($attributes->coupon->code)
        ) {
            $coupon = CouponsQuery::create()
                ->findOneByCode($attributes->coupon->code)
            ;

            // make sure the coupon's minimum purchase limit is met.
            if ($order->getTotalPrice(true) < $coupon->getMinPurchaseAmount()) {
                $invalid_order_message = $this->get('translator')->trans('order.amount.to.low.for.coupon', [
                    '%amount%' => Tools::moneyFormat($coupon->getAmount()),
                ], 'checkout');
            }

// if we need this again, we need to address it in the event listener.
//
//            // we sometimes see orders where the discount is missing from the orders_lines table, we re-add it.
//            $discount = OrdersLinesQuery::create()
//                ->joinWithProducts()
//                ->filterByType('discount')
//                ->filterByProductsName('coupon.code')
//                ->findByOrdersId($order->getId())
//            ;
//
//            if (!$discount instanceof OrdersLines) {
//                $order->setDiscountLine(
//                    $this->get('translator')->trans('coupon', [], 'checkout'),
//                    -$coupon->getAmount(),
//                    'coupon.code'
//                );
//            }
        }

        /* ------------------------------------------------- */
        $this->container->get('event_dispatcher')->dispatch('order.summery.updated', new FilterOrderEvent($order));
        /* ------------------------------------------------- */
        $order->save();
        $order->reload(true, Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ));
        /* ------------------------------------------------- */

        $attributes = array();
        foreach ($order->getOrdersAttributess() as $att) {
            $attributes[$att->getNs()][$att->getCKey()] = $att->getCValue();
        }

        $html = $this->render('CheckoutBundle:Default:summery.html.twig', array(
            'attributes'            => $attributes,
            'invalid_order_message' => $invalid_order_message,
            'order'                 => $order,
            'skip_my_account'       => true,
        ));

        // reset connection
        Propel::setForceMasterConnection(false);

        if ( $this->get('request')->isXmlHttpRequest() ) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => true,
                    'message' => '',
                    'data' => $html->getContent()
                ));
            }
        }

        return $html;
    }


    /**
     * success Action
     *
     * @return Response
     */
    public function successAction()
    {
        $order = OrdersPeer::getCurrent();
        $hanzo = Hanzo::getInstance();

        if ($order->isNew()) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        $shipping_total = 0;
        foreach ($order->getOrderLineShipping() as $line) {
            $shipping_total += $line->getPrice();
        }

        $data = [
            'city'        => $order->getBillingCity(),
            'country'     => $order->getBillingCountry(),
            'currency'    => $order->getCurrencyCode(),
            'expected_at' => $order->getExpectedDeliveryDate( 'd-m-Y' ),
            'id'          => $order->getId(),
            'shipping'    => number_format($shipping_total, 2, '.', ''),
            'state'       => $order->getBillingStateProvince(),
            'store_name'  => 'POMPdeLUX',
            'tax'         => 0,
            'total'       => number_format($order->getTotalPrice(), 2, '.', ''),
        ];

        foreach ($order->getOrdersLiness() as $line) {
            if ('product' != $line->getType()) {
                continue;
            }

            $data['lines'][] = [
                'name'      => $line->getProductsName(),
                'price'     => number_format($line->getPrice(), 2, '.', ''),
                'quantity'  => $line->getQuantity(),
                'sku'       => $line->getProductsSku(),
                'variation' => trim(str_replace($line->getProductsName(), '', $line->getProductsSku())),
            ];
        }


        // the order is now complete, so we remove it from the session
        $session = $hanzo->getSession();
        $session->remove('order_id');

        if ($session->has('in_edit')) {
            $this->get('event_dispatcher')->dispatch('order.edit.done', new FilterOrderEvent($order));
        }

        // one-to-one, we can only have one session_id or order in the database....
        $session->save();
        $session->migrate();

        // hf@bellcom.dk: used to avoid user pressing back on success page to get back to process, which then sends the customer to failed
        $session->set('last_successful_order_id', $order->getId());

        // update/set basket cookie
        Tools::setCookie('basket', '(0) '.Tools::moneyFormat(0.00), 0, false);

        return $this->render('CheckoutBundle:Default:success.html.twig', array(
            'order' => $data,
        ));
    }

    /**
     * failedAction
     *
     * @return Response
     **/
    public function failedAction()
    {
        $order = OrdersPeer::getCurrent();

        // Last check before we declare the order failed
        if ($order->getState() >= Orders::STATE_PAYMENT_OK) {
            return $this->redirect($this->generateUrl('_checkout_success'));
        }

        $this->get('event_dispatcher')->dispatch('order.payment.failed', new FilterOrderEvent($order));

        // The customer can't do anything with the order, so we remove it from the session
        $hanzo = Hanzo::getInstance();
        $session = $hanzo->getSession();
        $session->remove('order_id');
        $session->save();

        // one-to-one, we can only have one session_id or order in the database....
        $session->migrate();

        // update/set basket cookie
        Tools::setCookie('basket', '(0) '.Tools::moneyFormat(0.00), 0, false);

        return $this->render('CheckoutBundle:Default:failed.html.twig', array(
            'error' => '', // NICETO: pass error from paymentmodule to this page
            'order_id' => $order->getId()
        ));
    }


    public function testAction()
    {
        $order = OrdersPeer::getCurrent();

        if (($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        // trigger event, handles discounts and other stuff.
        $this->get('event_dispatcher')->dispatch('order.summery.finalize', new FilterOrderEvent($order));


        if ($order->isHostessOrder() && ($order->getTotalPrice() < 0)) {
            $this->get('session')->getFlashBag()->add('notice', $this->get('translator')->trans('order.amount.to.low', [], 'checkout'));
            return $this->redirect($this->generateUrl('basket_view'));
        }

        $order->save();
        $order->reload(true);

        return $this->render('CheckoutBundle:Default:flow.html.twig', array(
            'page_type'       => 'checkout',
            'order'           => $order,
            'skip_my_account' => true,
        ));
    }

}
