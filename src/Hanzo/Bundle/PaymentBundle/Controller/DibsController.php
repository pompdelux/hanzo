<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\AddressesPeer;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Bundle\PaymentBundle\Dibs\DibsApi;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class DibsController extends CoreController
{
    /**
     * callbackAction
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function callbackAction()
    {
        $api = $this->get('payment.dibsapi');

        $request = $this->get('request');
        $orderId = $request->get('orderid');

        if ( $orderId === false )
        {
            Tools::log( 'Dibs callback did not supply a valid order id' );
            Tools::log( $_POST );
            return new Response('Failed', 500, array('Content-Type' => 'text/plain'));
        }

        $order = OrdersPeer::retriveByPaymentGatewayId( $orderId );

        if ( !($order instanceof Orders) )
        {
            Tools::log( 'Dibs callback did not supply a valid order id: "'. $orderId .'"' );
            Tools::log( $_POST );
            return new Response('Failed', 500, array('Content-Type' => 'text/plain'));
        }

        try
        {
            $api->verifyCallback( $request, $order );
            $api->updateOrderSuccess( $request, $order );

            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
        }
        catch (Exception $e)
        {
            Tools::log( $e->getMessage() );
            $api->updateOrderFailed( $request, $order );
        }

        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }

    /**
     * cancelAction
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancelAction()
    {
        $translator = $this->get('translator');

        $this->get('session')->setFlash('notice', $translator->trans( 'payment.canceled', array(), 'checkout' ));

        return $this->redirect($this->generateUrl('_checkout'));
     }

    /**
     * blockAction
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $api = $this->get('payment.dibsapi');

        $isJson = ('json' === $this->getFormat() ) ? true : false;

        if ( !$api->isActive() )
        {
            if ( $isJson )
            {
                return $this->json_response( array('status' => false) );
            }
            else
            {
                return new Response( '', 200, array('Content-Type' => 'text/html'));
            }
        }

        $order = OrdersPeer::getCurrent();
        $gateway_id = $order->getPaymentGatewayId();

        if ( empty($gateway_id) ) // The first time the form is rendered we set some ekstra stuff
        {
            $gateway_id = Tools::getPaymentGatewayId();
            $order->setPaymentGatewayId($gateway_id);
            // No need to set state here, it should be handled else where

            // annoying, but performs better...
            if ('' == $order->getCurrencyCode()) {
                $order->setCurrencyCode(Hanzo::getInstance()->get('core.currency'));
            }

            $order->save();
        }

        if ( $order->getInEdit() && !$isJson) // New gateway id, but only when the request is not json
        {
            $gateway_id = Tools::getPaymentGatewayId();
            $order->setPaymentGatewayId($gateway_id);
            $order->save();
        }

        $settings = $api->buildFormFields(
            $order->getPaymentGatewayId(),
            $order
        );

        $cardtypes = $api->getEnabledPaytypes();

        if ( $isJson )
        {
            return $this->json_response( array('status' => true, 'fields' => $settings) );
        }
        else
        {
            return $this->render('PaymentBundle:Dibs:block.html.twig',array( 'cardtypes' => $cardtypes, 'form_fields' => $settings ));
        }
    }
}
