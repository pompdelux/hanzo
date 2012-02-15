<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request
    ;

use Hanzo\Core\Hanzo,
    Hanzo\Core\CoreController,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\Addresses,
    Hanzo\Model\AddressesPeer,
    Hanzo\Model\AddressesQuery,
    Hanzo\Model\CustomersPeer
    ;

use Hanzo\Bundle\ShippingBundle\ShippingMethods\ShippingMethod
    ;

use Exception
    ;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( $order->isNew() === true )
        {
            return $this->redirect($this->generateUrl('basket_view'));
        }

        return $this->render('CheckoutBundle:Default:index.html.twig',array('page_type'=>'checkout'));
    }

    /**
     * updateAction
     * @param string $block The block that has been updated
     * @param string $state State of the block
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateAction($block, $state)
    {
        $order = OrdersPeer::getCurrent();
        $t = $this->get('translator');

        if ( $order->isNew() )
        {
            return $this->json_response(array(
                'status' => false,
                'message' => $t->trans('json.err.no_order', array(), 'checkout'),
            ));
        }

        $request = $this->get('request');

        try
        {
            switch ($block) 
            {
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
        }
        catch ( Exception $e )
        {
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
     * @param Orders $order
     * @param Request $request
     * @param bool $state
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function updateAddress( Orders $order, Request $request, $state )
    {
        if ( $state === false )
        {
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

            if ( !($query instanceOf Addresses) )
            {
                // There should be 2 addresses at this point
                throw new Exception( 'No address could be found' );
            }

            switch ($type) 
            {
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
     * @param Orders $order
     * @param Request $request
     * @param bool $state
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function updateShipping( Orders $order, Request $request, $state )
    {
        if ( $state === false )
        {
            $order->setShippingMethod(null);
        }

        $shippingApi = $this->get('shipping.shippingapi');
        $data = $request->get('data');
        $t = $this->get('translator');

        $shippingMethodId = $data['selectedMethod'];

        $methods = $shippingApi->getMethods();

        if ( !isset($methods[$shippingMethodId]) )
        {
            throw new Exception( $t->trans('err.unknown_shipping_method', array(), 'checkout') );
        }

        $method = $methods[$shippingMethodId];

        $order->setShippingMethod( $shippingMethodId );
        $order->setOrderLineShipping( $method, ShippingMethod::TYPE_NORMAL );
        if ( $method->hasFee() )
        {
            $order->setOrderLineShipping( $method, ShippingMethod::TYPE_FEE );
        }
    }

    /**
     * updatePayment
     * @todo: should state be uses to something?
     * @param Orders $order
     * @param Request $request
     * @param bool $state
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function updatePayment( Orders $order, Request $request, $state )
    {
        $data = $request->get('data');
        // TODO: match CustPaymMode from old system?
        $order->setPaymentMethod( $data['selectedMethod'] );
        $order->setPaymentPaytype( $data['selectedPaytype'] );

        // Some payforms have a fee
        if ( $data['selectedMethod'] == 'gothia' )
        {
            $order->setOrderLinePaymentFee( 'gothia', 29.00, 0, 91 );
        }
    }

    /**
     * Validate
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function validateAction()
    {
        // TODO: make this usefull
        $order = OrdersPeer::getCurrent();

        try
        {
          $this->validateShipping( $order );
        }
        catch (Exception $e)
        {
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
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function validateShipping( Orders $order )
    {
    }

    /**
     * summeryAction
     *
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function summeryAction()
    {
        $order = OrdersPeer::getCurrent();
        $orderAttributes = $order->getOrdersAttributess();

        $attributes = array();

        foreach ($orderAttributes as $att)
        {
            $attributes[$att->getNs()][$att->getCKey()] = $att->getCValue();
        }

        if ( $this->get('request')->isXmlHttpRequest() )
        {
            return json_encode('hest'); // FIXME: return something usefull
        }

        return $this->render('CheckoutBundle:Default:summery.html.twig',array('order'=>$order, 'attributes' => $attributes));
    }

    /**
     * addressesAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function addressesAction()
    {
        // TODO: should we take the addresses from the order?
        $customer = CustomersPeer::getCurrent();
        $customerAddresses = $customer->getAddresses();

        $addresses = array();

        $shippingApi = $this->get('shipping.shippingapi');

        foreach ($customerAddresses as $address) 
        {
            $addresses[$address->getType()] = $address;
        }

        if ( !isset($addresses['shipping']) && !isset($addresses['payment']) )
        {
            return $this->render('CheckoutBundle:Default:addresses.html.twig', array( 'no_addresses' => true ));
        }

        // Only a payment address exists, create a shipping address based on the payment address
        if ( !isset($addresses['shipping']) && isset($addresses['payment']) )
        {
            $shipping = $addresses['payment']->copy();
            $shipping->setType('shipping');
            $shipping->save();
            $addresses['shipping'] = $shipping;
        }

        // Same as above just for payment
        if ( !isset($addresses['payment']) && isset($addresses['shipping']) )
        {
            $payment = $addresses['shipping']->copy();
            $payment->setType('payment');
            $payment->save();
            $addresses['payment'] = $payment;
        }

        // TODO: the address should be created here minus the fields
        $hasOvernightBox = $shippingApi->isMethodAvaliable(12); // Døgnpost

        return $this->render('CheckoutBundle:Default:addresses.html.twig', array( 'addresses' => $addresses, 'has_overnight_box' => $hasOvernightBox ));
    }

    /**
     * confirmAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function confirmAction()
    {
        return $this->render('CheckoutBundle:Default:confirm.html.twig');
    }
}
