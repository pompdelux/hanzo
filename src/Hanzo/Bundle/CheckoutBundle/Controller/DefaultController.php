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

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

use Exception;
use Propel;
use BasePeer;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( ($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        // trigger event, handles discounts and other stuff.
        $this->get('event_dispatcher')->dispatch('order.summery.finalize', new FilterOrderEvent($order));


        if ($order->isHostessOrder() && ($order->getTotalPrice() < 0)) {
            $this->get('session')->setFlash('notice', $this->get('translator')->trans('order.amount.to.low', [], 'checkout'));
            return $this->redirect($this->generateUrl('basket_view'));
        }

        $order->save();

        return $this->render('CheckoutBundle:Default:flow.html.twig', array(
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
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
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
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
        if ( $state === false ) {
            $order->clearBillingAddress();
            $order->clearDeliveryAddress();
            return;
        }

        if ($order->getCustomersId()) {
            $customer = $order->getCustomers();
        } else {
            $customer = CustomersPeer::getCurrent();
        }
        $data = $request->get('data');

        if ( !isset($data['addresses']) ) {
            throw new Exception( 'No address could be found' );
        }

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

                 // Note that setDeliveryAddress checks if the type is correct
                case 'overnightbox':
                    if (12 == $order->getDeliveryMethod()) {
                      $order->setDeliveryAddress( $query );
                    }
                  break;
                case 'shipping':
                    $order->setDeliveryAddress( $query );
                    break;
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
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
        if ( $state === false ) {
            $order->setDeliveryMethod(null);
        }

        $shippingApi = $this->get('shipping.shippingapi');
        $data = $request->get('data');
        $t = $this->get('translator');

        $shippingMethodId = $data['selectedMethod'];

        $methods = $shippingApi->getMethods();

        if ( !isset($methods[$shippingMethodId]) ) {
            throw new Exception( $t->trans('err.unknown_shipping_method', [], 'checkout') );
        }

        $method = $methods[$shippingMethodId];

        $order->setDeliveryMethod( $shippingMethodId );
        $order->setOrderLineShipping( $method, ShippingMethods::TYPE_NORMAL );
        if ( $method->getFee() ) {
            $order->setOrderLineShipping( $method, ShippingMethods::TYPE_FEE );
        }
    }

    /**
     * updatePayment
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @param Orders $order
     * @param Request $request
     * @param bool $state
     * @return void
     **/
    protected function updatePayment( Orders $order, Request $request, $state )
    {
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
        if ( $state === 'false' )
        {
            throw new Exception( 'Payment state not valid' );
        }

        if ( $order->getState() >= Orders::STATE_PRE_PAYMENT )
        {
            $trans = $this->get('translator');
            throw new Exception( $trans->trans('order.state_pre_payment.locked', [], 'checkout') );
        }

        $data = $request->get('data');
        $order->setPaymentMethod( $data['selectedMethod'] );
        $order->setPaymentPaytype( $data['selectedPaytype'] );

        $api = $this->get('payment.'.$data['selectedMethod'].'api');

        // Handle payment fee
        // Currently hardcoded to 0 vat
        // It also only supports one order line with payment fee, as all others are deleted
        $order->setOrderLinePaymentFee( $data['selectedMethod'], $api->getFee(), 0, $api->getFeeExternalId() );
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
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
        $order = OrdersPeer::getCurrent();
        $trans = $this->get('translator');

        if ($order->getState() >= Orders::STATE_PRE_PAYMENT) {
            return $this->json_response(array(
                'status' => false,
                'message' => $trans->trans('order.state_pre_payment.locked', [], 'checkout'),
                'data' => array(
                    'name' => 'payment'
                ),
            ));
        }

        $data = $this->get('request')->get('data');
        $grouped = array();

        foreach ($data as $values) {
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
                    'name' => 'payment'
                ),
            ));
        }

        try {
            $this->validateAddresses( $order, $grouped['address'] );
        } catch (Exception $e) {
            return $this->json_response(array(
                'status' => false,
                'message' => $e->getMessage(),
                'data' => array(
                    'name' => 'shipping'
                ),
            ));
        }


        // If the customer cancels payment, state is back to building
        // Customer is only allowed to add products to the basket if state is >= pre payment
        $order->setState( Orders::STATE_PRE_PAYMENT );
        $order->save();

        return $this->json_response(array(
            'status' => true,
            'message' => 'Ok',
        ));
    }

    /**
     * validateAddresses
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function validateAddresses( Orders $order, $data )
    {
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
      $fields = $order->toArray(BasePeer::TYPE_FIELDNAME);

      $check = array(
          'billing_first_name',
          'billing_last_name',
          'billing_address_line_1',
          'billing_postal_code',
          'billing_city',

          'delivery_first_name',
          'delivery_last_name',
          'delivery_address_line_1',
          'delivery_postal_code',
          'delivery_city',
          );

      $missing = array();
      foreach ($check as $field)
      {
          if (!isset($fields[$field])) {
              $missing[] = $field;
          }
      }

      // TODO: translation
      if ( !empty($missing) ) {
        throw new Exception( 'Et, eller flere felter mangler i dine adresser' );
      }
    }

    /**
     * validatePayment
     * @NICETO: low priority: more validation
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function validatePayment( Orders $order, $data )
    {
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
        $api = $this->get('payment.'. $data['selectedMethod'] .'api');

        if (!$api->isActive()) {
            // todo: low priority: translation
            throw new Exception( 'Selected payment type is not avaliable' );
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
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
        $t = $this->get('translator');

        if (($data['state'] !== 'true')) {
            // todo: low priority: translation
            throw new Exception( 'State is not correct' );
        }

        $shippingApi = $this->get('shipping.shippingapi');

        $methods = $shippingApi->getMethods();

        if ( !isset($methods[ $data['selectedMethod']  ]) ) {
            throw new Exception( $t->trans('err.unknown_shipping_method', [], 'checkout') );
        }
    }

    /**
     * addressesAction
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return void
     **/
    public function addressesAction($skip_empty = false)
    {
Tools::log('skal ikke bruges, find ud af hvor kaldet kommer fra !');
        $order = OrdersPeer::getCurrent();
        $customer = $order->getCustomers();

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

        $hasOvernightBox = $shippingApi->isMethodAvaliable(12); // Døgnpost

        // we need to have an overnightbox address if the service is available.... for now....
        if ($hasOvernightBox && empty($addresses['overnightbox'])) {
            $address = $addresses['payment']->copy();
            $address->setType('overnightbox');
            $address->setAddressLine1('');
            $address->setAddressLine2(null);
            $address->setCompanyName(null);
            $address->save();
            $addresses['overnightbox'] = $address;
        }

        if ($skip_empty || ($order instanceof Orders)) {
            if (empty($addresses['overnightbox'])) {
                $hasOvernightBox = false;
            }
        }

        if ($order->getDeliveryMethod() != 11) {
            $addresses['shipping']->setCompanyName(null);
        }

        return $this->render('CheckoutBundle:Default:addresses.html.twig', array(
          'addresses' => $addresses,
          'has_overnight_box' => $hasOvernightBox
        ));
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

        if (empty($attributes->global->HomePartyId)) {
            $key = str_replace('Sales', '', $domain_key);
            $order->setAttribute('HomePartyId', 'global', 'WEB ' . $key);
            $order->setAttribute('SalesResponsible', 'global', 'WEB ' . $key);
        }

        if (empty($attributes->global->domain_name)) {
            $order->setAttribute('domain_name', 'global', $_SERVER['HTTP_HOST']);
        }

        if (empty($attributes->global->domain_key)) {
            $order->setAttribute('domain_key', 'global', $domain_key);
        }

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
        $session->save();

        if ($session->has('in_edit')) {
            $this->get('event_dispatcher')->dispatch('order.edit.done', new FilterOrderEvent($order));
        }

        // one-to-one, we can only have one session_id or order in the database....
        $session->migrate();

        // hf@bellcom.dk: used to avoid user pressing back on success page to get back to process, which then sends the customer to failed
        $session->set('last_successful_order_id',$order->getId());

        return $this->render('CheckoutBundle:Default:success.html.twig', array(
            'order_id' => $order->getId(),
            'expected_at' => $order->getExpectedDeliveryDate( 'd-m-Y' ),
        ));
    }

    /**
     * failedAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function failedAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( $order->getState() >= Orders::STATE_PAYMENT_OK ) // Last check before we declare the order failed
        {
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

        return $this->render('CheckoutBundle:Default:failed.html.twig', array(
            'error' => '', // NICETO: pass error from paymentmodule to this page
            'order_id' => $order->getId()
            ));
    }

    /**
     * populateOrderAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function populateOrderAction()
    {
Tools::log('skal ikke bruges - eller ?? , find ud af hvor kaldet kommer fra !');
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
                // Dibs call it V-DK, we call it DK :)
                if ( strtoupper( $orderArray['PaymentMethod'] ) == 'V-DK' )
                {
                    $orderArray['PaymentMethod'] = 'DK';
                }
                if ( strtoupper( $orderArray['PaymentMethod'] ) == 'MC(DK)' )
                {
                    $orderArray['PaymentMethod'] = 'MC';
                }
                if ( strtoupper( $orderArray['PaymentMethod'] ) == 'MC(SE)' )
                {
                    $orderArray['PaymentMethod'] = 'MC';
                }
                if ( strtoupper( $orderArray['PaymentMethod'] ) == 'VISA(SE)' )
                {
                    $orderArray['PaymentMethod'] = 'VISA';
                }
                break;
            }
        }

        $orderArray['BillingMethod']  = $order['BillingMethod'];
        $orderArray['DeliveryMethod'] = $order['DeliveryMethod'];

        return $this->json_response( array('error' => false, 'order' => $orderArray) );
    }


    public function testAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( ($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        // trigger event, handles discounts and other stuff.
        $this->get('event_dispatcher')->dispatch('order.summery.finalize', new FilterOrderEvent($order));


        if ($order->isHostessOrder() && ($order->getTotalPrice() < 0)) {
            $this->get('session')->setFlash('notice', $this->get('translator')->trans('order.amount.to.low', [], 'checkout'));
            return $this->redirect($this->generateUrl('basket_view'));
        }

        $order->save();
        $order->reload(true);

        return $this->render('CheckoutBundle:Default:flow.html.twig', array(
            'page_type' => 'checkout',
            'order'     => $order
        ));
    }

}
