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
            // TODO: is this the right way todo it? passing request seems wrong
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
        error_log(__LINE__.':'.__FILE__.' '.print_r($_POST,1)); // hf@bellcom.dk debugging
        error_log(__LINE__.':'.__FILE__.' '.print_r($_GET,1)); // hf@bellcom.dk debugging
        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }

    /**
     * apiTestAction
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function apiTestAction($method)
    {
        $api = $this->get('payment.dibsapi');

        //$api = new DibsApi();
        //$apiResponse = $api->call()->acquirersStatus();
        //$apiResponse = $api->call()->payinfo( 527221861 );
        //error_log(__LINE__.':'.__FILE__.' '.print_r($_SESSION,1)); // hf@bellcom.dk debugging

        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }

    /**
     * formTestAdction
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function formTestAction()
    {
        $api = $this->get('payment.dibsapi');

        $order = OrdersPeer::getCurrent();
        $settings = $api->buildFormFields( $order );

        $form = '<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
            <html lang="da">
            <head>
            <title>POMPdeLUX - TEST</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            </head>
            <body>
            '. $this->renderView('PaymentBundle:Dibs:form.html.twig',$settings) .'
            </body>
            </html>';

        return new Response( $form, 200, array('Content-Type' => 'text/html'));
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

        if ( !$api->isActive() )
        {
            return new Response( '', 200, array('Content-Type' => 'text/html'));
        }

        $api = $this->get('payment.dibsapi');

        $gateway_id = Tools::getPaymentGatewayId();
        $order = OrdersPeer::getCurrent();
        $order->setPaymentGatewayId($gateway_id);

        // annoying, but performs better...
        if ('' == $order->getCurrencyCode()) {
            $order->setCurrencyCode(Hanzo::getInstance()->get('core.currency'));
        }

        $settings = $api->buildFormFields(
            $gateway_id,
            Hanzo::getInstance()->get('core.locale'),
            $order
        );

        // FIXME: hardcoded vars
        $cardtypes = array(
            'DK' => true,
            'VISA' => true,
            'ELEC' => true,
            'MC' => true
        );

        return $this->render('PaymentBundle:Dibs:block.html.twig',array( 'cardtypes' => $cardtypes, 'form_fields' => $settings ));
    }
}
