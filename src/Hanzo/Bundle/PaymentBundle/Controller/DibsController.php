<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApi;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class DibsController extends CoreController
{
    /**
     * callbackAction
     *
     * @param Request $request
     *
     * @return Response
     */
    public function callbackAction(Request $request)
    {
        $api = $this->get('payment.dibsapi');

        $payment_gateway_id = false;
        if ($request->request->has('orderId')) {
            $payment_gateway_id = $request->request->get('orderId');
        } elseif ($request->request->has('orderid')) {
            $payment_gateway_id = $request->request->get('orderid');
        }

        if (false === $payment_gateway_id) {
            Tools::log('Dibs callback did not supply a valid payment gateway id');
            Tools::log($_POST);
            return new Response('Failed', 500, array('Content-Type' => 'text/plain'));
        }

        $order = OrdersPeer::retriveByPaymentGatewayId($payment_gateway_id);

        if (!($order instanceof Orders)) {
            Tools::log('No order matched payment gateway id: "'. $payment_gateway_id .'"');
            Tools::log($_POST);
            return new Response('Failed', 500, array('Content-Type' => 'text/plain'));
        }

        try {
            $api->verifyCallback($request, $order);
            $api->updateOrderSuccess($request, $order);

            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
        } catch (Exception $e) {
            Tools::log($e->getMessage());
            $api->updateOrderFailed($request, $order);
        }

        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }


    /**
     * blockAction
     *
     * @return Response
     */
    public function blockAction()
    {
        $api = $this->get('payment.dibsapi');
        $redis = $this->get('pdl.phpredis.permanent');

        $dibs_status = $redis->hget('service.status', 'dibs');

        $isJson = ('json' === $this->getFormat() ) ? true : false;

        if (!$api->isActive() || ('DOWN' == $dibs_status)) {
            if ($isJson) {
                return $this->json_response( array('status' => false) );
            } else {

                $html = '';
                if ('DOWN' == $dibs_status) {
                    $html = '<div class="down">'.$this->get('translator')->trans('dibs.down.message', [], 'checkout').'</div>';
                }

                return new Response( $html, 200, array('Content-Type' => 'text/html'));
            }
        }

        $order = OrdersPeer::getCurrent();
        $settings = $api->buildFormFields($order);
        $cardtypes = $api->getEnabledPaytypes();

        if ($isJson) {
            return $this->json_response( array('status' => true, 'fields' => $settings) );
        } else {
            return $this->render('PaymentBundle:Dibs:block.html.twig',array( 'cardtypes' => $cardtypes, 'form_fields' => $settings ));
        }
    }

    /**
     * stateCheckAction
     *
     * Checks the current state of the order, this should allow the callback from dibs to be completed
     *
     * @return array
     */
    public function stateCheckAction()
    {
        $order   = OrdersPeer::getCurrent();
        $goto    = false;
        $session = $this->get('session');

        if ($session->has('last_successful_order_id')) {
            $goto = true;
        }

        return $this->json_response( array('state' => $order->getState(), 'redirect_to_basket' => $goto ) );
    }

    /**
     * processAction
     *
     * This shows the customer a page that checks the state of the ordre until it is correct (<= payment ok ) or fails
     *
     * @param int $order_id
     *
     * @return Response
     */
    public function processAction($order_id)
    {
        $order = OrdersPeer::retriveByPaymentGatewayId( $order_id );

        if (!empty($order) && ($order->getId() !== $this->get('session')->get('order_id'))) {
            error_log(__LINE__.':'.__FILE__.' Order id mismatch, in url: '.$order_id. ' in session: '. $this->getRequest()->getSession()->get('order_id') ); // hf@bellcom.dk debugging
        }

        return $this->render('PaymentBundle:Default:process.html.twig', [
            'hide_mini_basket' => true,
            'skip_my_account'  => true,
        ]);
    }
}
