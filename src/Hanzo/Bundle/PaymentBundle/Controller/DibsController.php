<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApi;

class DibsController extends CoreController
{
    /**
     * callbackAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function callbackAction()
    {
        $api = new DibsApi();

        $request = $this->get('request');
        $orderId = $request->get('orderid');

        // FIXME: testing
        $orderId = 1;

        if ( $orderId === false )
        {
            throw new Exception( 'Dibs callback did not supply a valid order id' );
        }

        //$order = OrdersPeer::getCurrent();
        $order = OrdersPeer::retrieveByPK( $orderId );

        if ( !($order instanceof Orders) )
        {
            throw new Exception( 'Dibs callback did not supply a valid order id: "'. $orderId .'"' );
        }

        try
        {
            $api->verifyCallback( $request, $order );
            // TODO: is this the right way todo it? passing request seems wrong
            $api->updateOrderSuccess( $request, $order );
        }
        catch (Exception $e)
        {
            $api->updateOrderFailed( $request, $order );
        }

        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }

    /**
     * okAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function okAction()
    {
        error_log(__LINE__.':'.__FILE__.' '.print_r($_GET,1)); // hf@bellcom.dk debugging
        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }

    /**
     * cancelAction
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
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function formTestAction()
    {
        $api = new DibsApi();
        $orderID = 'test_'.date('His');
        $amount = 41500;

        $form = '<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
            <html lang="da">
            <head>
            <title>POMPdeLUX - TEST</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            </head>
            <body>
            <form name="dibs_payment_info" action="https://payment.architrade.com/paymentweb/start.action" method="post">
            <input type="text" name="merchant" value="90057323" />
            <input type="text" name="orderid" value="'. $orderID .'" />
            <input type="text" name="lang" value="da" />
            <input type="text" name="amount" value="'.$amount.'" />
            <input type="text" name="currency" value="208" />
            <input type="text" name="cancelurl" value="http://hanzo.dk/app_dev.php/payment/dibs/cancel" />
            <input type="text" name="callbackurl" value="http://hanzo.dk/app_dev.php/payment/dibs/callback" />
            <input type="text" name="accepturl" value="http://hanzo.dk/app_dev.php/payment/dibs/ok" />
            <input type="text" name="skiplastpage" value="YES" />
            <input type="text" name="uniqueoid" value="YES" />
            <input type="text" name="test" value="YES" />
            <input type="text" name="paytype" value="DK" />
            <input type="text" name="md5key" value="'. $api->md5( $orderID, 208, $amount )  .'" />
            <input type="submit" value="Fortsæt" alt="Fortsæt" />
            </form>
            </body>
            </html>';

        return new Response( $form, 200, array('Content-Type' => 'text/html'));
    }

    public function indexAction($name)
    {
        return $this->render('PaymentBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * blockAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        return new Response('Dibs payment block', 200, array('Content-Type' => 'text/html'));
    }
}
