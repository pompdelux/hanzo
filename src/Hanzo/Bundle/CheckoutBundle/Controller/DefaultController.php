<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;

use Hanzo\Model\ShippingMethods;

use Exception;
use Propel;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( $order->isNew() === true ) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        return $this->render('CheckoutBundle:Default:index.html.twig', array(
            'page_type' => 'checkout'
        ));
    }

    /**
     * updateAction
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @param string $block The block that has been updated
     * @param string $state State of the block
     * @return Response
     **/
    public function updateAction($block, $state)
    {
        $order = OrdersPeer::getCurrent();
        $t = $this->get('translator');

        if ( $order->isNew() ) {
            return $this->json_response(array(
                'status' => false,
                'message' => $t->trans('json.err.no_order', array(), 'checkout'),
            ));
        }

        $request = $this->get('request');

        try {
            switch ($block) {
                case 'shipping':
                    $this->updateShipping( $order, $request, $state );
                    break;
                case 'address':
                    $this->updateAddress( $order, $request, $state );
                    break;
                case 'payment':
                    $this->updatePayment( $order, $request, $state );
                    break;
                case 'summery':
                    // code...
                    break;
                case 'confirm':
                    // code...
                    break;
                default:
                    throw new Exception( 'Unknown block' );
                    break;
            }

            $order->save();

            return $this->json_response(array(
                'status' => true,
                'message' => '',
            ));
        } catch ( Exception $e ) {
            return $this->json_response(array(
                'status' => false,
                'message' => $e->getMessage(),
                'data' => array(
                    'name' => $block
                ),
            ));
        }

    }

    /**
     * updateAddress
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @param Orders $order
     * @param Request $request
     * @param bool $state
     * @return void
     **/
    protected function updateAddress( Orders $order, Request $request, $state )
    {
        if ( $state === false ) {
            $order->clearBillingAddress();
            $order->clearDeliveryAddress();
            return;
        }

        $customer = CustomersPeer::getCurrent();

        $data = $request->get('data');
        $addressTypes = $data['addresses'];

        $order->setFirstName( $customer->getFirstName() )
            ->setLastName( $customer->getLastName() );

        $addresses = array();

        foreach ($addressTypes as $type)
        {
            $query = AddressesQuery::create()
                ->filterByCustomersId( $customer->getId() )
                ->filterByType( $type )
                ->findOne();

            if ( !($query instanceOf Addresses) ) {
                // There should be 2 addresses at this point
                throw new Exception( 'No address could be found' );
            }

            switch ($type) {
                case 'payment':
                    $order->setBillingAddress( $query );
                    break;
                case 'shipping':
                    $order->setDeliveryAddress( $query );
                    break;
                    // TODO: Døgnpost?
            }
        }
    }

    /**
     * updateShipping
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @param Orders $order
     * @param Request $request
     * @param bool $state
     * @return void
     **/
    protected function updateShipping( Orders $order, Request $request, $state )
    {
        if ( $state === false ) {
            $order->setShippingMethod(null);
        }

        $shippingApi = $this->get('shipping.shippingapi');
        $data = $request->get('data');
        $t = $this->get('translator');

        $shippingMethodId = $data['selectedMethod'];

        $methods = $shippingApi->getMethods();

        if ( !isset($methods[$shippingMethodId]) ) {
            throw new Exception( $t->trans('err.unknown_shipping_method', array(), 'checkout') );
        }

        $method = $methods[$shippingMethodId];

        $order->setShippingMethod( $shippingMethodId );
        $order->setOrderLineShipping( $method, ShippingMethods::TYPE_NORMAL );
        if ( $method->getFee() ) {
            $order->setOrderLineShipping( $method, ShippingMethods::TYPE_FEE );
        }
    }

    /**
     * updatePayment
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @todo: lav priority: should state be uses to something?
     * @param Orders $order
     * @param Request $request
     * @param bool $state
     * @return void
     **/
    protected function updatePayment( Orders $order, Request $request, $state )
    {
        $data = $request->get('data');
        // TODO: match CustPaymMode from old system?
        $order->setPaymentMethod( $data['selectedMethod'] );
        $order->setPaymentPaytype( $data['selectedPaytype'] );

        $api = $this->get('payment.'.$data['selectedMethod'].'api');

        // Handle payment fee
        // Currently hardcoded to 0 vat
        // It also only supports one order line with payment fee, as all others are deleted
        $order->setOrderLinePaymentFee( $data['selectedMethod'], $api->getFee(), 0, $api->getFeeExternalId() );

        // If the customer goes back to the basket, state is back to building
        $order->setState( Orders::STATE_PRE_PAYMENT );
        $order->save();
    }

    /**
     * Validate
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return Response
     **/
    public function validateAction()
    {
        $order = OrdersPeer::getCurrent();

        $data = $this->get('request')->get('data');
        $grouped = array();

        foreach ($data as $values) 
        {
            $grouped[$values['name']] = $values;
        }

        try {
            $this->validateShipping( $order, $grouped['shipping'] );
        } catch (Exception $e) {
            return $this->json_response(array(
                'status' => false,
                'message' => $e->getMessage(),
                'data' => array(
                    'name' => 'shipping'
                ),
            ));
        }

        try {
            $this->validatePayment( $order, $grouped['payment'] );
        } catch (Exception $e) {
            return $this->json_response(array(
                'status' => false,
                'message' => $e->getMessage(),
                'data' => array(
                    'name' => 'shipping'
                ),
            ));
        }

        return $this->json_response(array(
            'status' => true,
            'message' => 'Ok',
        ));
    }

    /**
     * validatePayment
     * @todo: low priority: more validation
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function validatePayment( Orders $order, $data )
    {
        $api = $this->get('payment.'. $data['selectedMethod'] .'api');

        if ( !$api->isActive() )
        {
            throw new Exception( 'Selected payment type is not avaliable' ); // todo: low priority: translation
        }
    }

    /**
     * validateShipping
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return void
     **/
    protected function validateShipping( Orders $order, $data ) 
    {
        $t = $this->get('translator');

        if ( $data['state'] !== 'true' )
        {
          throw new Exception( 'State is not correct' );  // todo: low priority: translation
        }

        $shippingApi = $this->get('shipping.shippingapi');

        $methods = $shippingApi->getMethods();

        if ( !isset($methods[ $data['selectedMethod']  ]) ) {
            throw new Exception( $t->trans('err.unknown_shipping_method', array(), 'checkout') );
        }
    }

    /**
     * addressesAction
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return void
     **/
    public function addressesAction($skip_empty = false, $order = null)
    {
        // TODO: should we take the addresses from the order?
        $customer = CustomersPeer::getCurrent();
        $customerAddresses = $customer->getAddresses();

        $addresses = array();

        $shippingApi = $this->get('shipping.shippingapi');

        foreach ($customerAddresses as $address) {
            $addresses[$address->getType()] = $address;
        }

        if ( !isset($addresses['shipping']) && !isset($addresses['payment']) ) {
            return $this->render('CheckoutBundle:Default:addresses.html.twig', array( 'no_addresses' => true ));
        }

        // Only a payment address exists, create a shipping address based on the payment address
        if ( !isset($addresses['shipping']) && isset($addresses['payment']) ) {
            $shipping = $addresses['payment']->copy();
            $shipping->setType('shipping');
            $shipping->save();
            $addresses['shipping'] = $shipping;
        }

        // Same as above just for payment
        if ( !isset($addresses['payment']) && isset($addresses['shipping']) ) {
            $payment = $addresses['shipping']->copy();
            $payment->setType('payment');
            $payment->save();
            $addresses['payment'] = $payment;
        }

        // TODO: the address should be created here minus the fields
        $hasOvernightBox = $shippingApi->isMethodAvaliable(12); // Døgnpost

        if ($skip_empty || ($order instanceof Orders)) {
            if (empty($addresses['overnightbox'])) {
                $hasOvernightBox = false;
            }
            if ($order->getDeliveryMethod() == 11) {
                unset($addresses['shipping']);
            }
        }

        return $this->render('CheckoutBundle:Default:addresses.html.twig', array( 'addresses' => $addresses, 'has_overnight_box' => $hasOvernightBox ));
    }


    /**
     * confirmAction
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return Response
     **/
    public function confirmAction()
    {
        return $this->render('CheckoutBundle:Default:confirm.html.twig');
    }


    /**
     * summeryAction
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return Response
     **/
    public function summeryAction()
    {
        // we only want the masterdata here, no slaves thank you...
        Propel::setForceMasterConnection(true);

        $order = OrdersPeer::getCurrent();
        $hanzo = Hanzo::getInstance();

        // first we finalize the order, aka. setting misc order attributes and updating lines ect.

        // 1. order product lines
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
            $product->setOriginalPrice($product_prices[$product->getProductsId()]['normal']['raw_price']);
            $product->setUnit(preg_replace('/[^a-z\.]/i', '', $product_units[$product->getProductsId()]));
            $product->save();
        }

        // 2. set currency code
        $order->setCurrencyCode($hanzo->get('core.currency'));

        // 3. register domain et-al
        $order->setAttribute('domain_name', 'global', $_SERVER['HTTP_HOST']);
        $order->setAttribute('domain_key', 'global', $hanzo->get('core.domain_key'));
        $order->setAttribute('HomePartyId', 'global', 'WEB ' . $hanzo->get('core.domain_key'));
        $order->setAttribute('SalesResponsible', 'global', 'WEB ' . $hanzo->get('core.domain_key'));

        $order->save();

        /* ------------------------------------------------- */

        $attributes = array();
        foreach ($order->getOrdersAttributess() as $att) {
            $attributes[$att->getNs()][$att->getCKey()] = $att->getCValue();
        }

        $html = $this->render('CheckoutBundle:Default:summery.html.twig', array(
            'order'=> $order,
            'attributes' => $attributes
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

        // the order is now complete, so we remove it from the session
        $session = $hanzo->getSession();
        $session->remove('order_id');

        if ($session->has('in_edit')) {
            $this->get('event_dispatcher')->dispatch('order.edit.cancel', new FilterOrderEvent($order));
        }

        // one-to-one, we can only have one session_id or order in the database....
        $session->migrate();

        // TODO: expected_in needs to be loaded from the database.
        return $this->render('CheckoutBundle:Default:success.html.twig', array(
            'order_id' => $order->getId(),
            'expected_in' => 2
        ));
    }

    /**
     * failedAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function failedAction()
    {
        return new Response('Ups'); // FIXME
    }

    /**
     * populateOrderAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function populateOrderAction()
    { 
        $orderObj      = OrdersPeer::getCurrent();
        $attributesObj = $orderObj->getOrdersAttributess();
        $order         = $orderObj->toArray();
        $attributes    = $attributesObj->toArray();
        $orderArray    = array();

        foreach ($attributes as $attribute) 
        {
            if ( $attribute['Ns'] == 'payment' && $attribute['CKey'] == 'paytype' ) 
            {
                $orderArray['PaymentMethod'] = $attribute['CValue'];
                break;
            }
        }

        $orderArray['BillingMethod']  = $order['BillingMethod'];
        $orderArray['DeliveryMethod'] = $order['DeliveryMethod'];

        return $this->json_response( array('error' => false, 'order' => $orderArray) );
    }
}
