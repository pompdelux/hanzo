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
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CustomersPeer;

use Hanzo\Model\ShippingMethods;

use Exception;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( $order->isNew() === true ) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        // set

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
     * @todo: should state be uses to something?
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

        // Some payforms have a fee
        if ( $data['selectedMethod'] == 'gothia' ) {
            $order->setOrderLinePaymentFee( 'gothia', 29.00, 0, 91 );
        }
    }

    /**
     * Validate
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return Response
     **/
    public function validateAction()
    {
        // TODO: make this usefull
        $order = OrdersPeer::getCurrent();

        try {
          $this->validateShipping( $order );
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
     * validateShipping
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return void
     **/
    protected function validateShipping( Orders $order ){}

    /**
     * summeryAction
     *
     * @author Henrik Farre <hf@bellcom.dk>
     * @return Response
     **/
    public function summeryAction()
    {
        $order = OrdersPeer::getCurrent();
        $orderAttributes = $order->getOrdersAttributess();

        $attributes = array();
        foreach ($orderAttributes as $att) {
            $attributes[$att->getNs()][$att->getCKey()] = $att->getCValue();
        }

        $html = $this->render('CheckoutBundle:Default:summery.html.twig', array(
            'order'=> $order,
            'attributes' => $attributes
        ));

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
     * success Action
     *
     * @return Response
     */
    public function successAction()
    {
        $order = OrdersPeer::getCurrent();

        if ($order->isNew()) {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        // the order is now complete, so we remove it from the session
        $session = Hanzo::getInstance()->getSession();
        $session->remove('order_id');

        // one-to-one, we can only have one session_id or order in the database....
        $session->migrate();

        // TODO: expected_in needs to be loaded from the database.
        return $this->render('CheckoutBundle:Default:success.html.twig', array(
            'order_id' => $order->getId(),
            'expected_in' => 2
        ));
    }
}
