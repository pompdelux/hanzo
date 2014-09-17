<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApi;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DibsController
 * @package Hanzo\Bundle\PaymentBundle
 */
class DibsController extends CoreController
{
    /**
     * Handles callbacks from DIBS payment service in the payment flow
     *
     * @param  Request $request
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
            Tools::log('Dibs callback did not supply a valid payment gateway id POST: '.print_r($_POST, 1).' L: '.$request->getLocale());

            return new Response('Failed', 500, ['Content-Type' => 'text/plain']);
        }

        $order = OrdersPeer::retriveByPaymentGatewayId($payment_gateway_id);

        if (!($order instanceof Orders)) {
            Tools::log('No order matched payment gateway id: "'. $payment_gateway_id .'" POST: '.print_r($_POST, 1).' L: '.$request->getLocale());

            return new Response('Failed', 500, ['Content-Type' => 'text/plain']);
        }

        try {
            $api->verifyCallback($request, $order);
            $api->updateOrderSuccess($request, $order);

            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
        } catch (\Exception $e) {
            Tools::log($e->getMessage());
            $api->updateOrderFailed($request, $order);
        }

        return new Response('Ok', 200, ['Content-Type' => 'text/plain']);
    }


    /**
     * blockAction
     *
     * @return Response
     */
    public function blockAction()
    {
Tools::log('YEAH - is used ..., delete log if seen.! <un>');
        $api         = $this->get('payment.dibsapi');
        $redis       = $this->get('redis.permanent');
        $dibs_status = $redis->hget('service.status', 'dibs');
        $isJson      = ('json' === $this->getFormat()) ? true : false;

        if (!$api->isActive() || ('DOWN' == $dibs_status)) {
            if ($isJson) {
                return $this->json_response(['status' => false]);
            } else {

                $html = '';
                if ('DOWN' == $dibs_status) {
                    $html = '<div class="down">'.$this->get('translator')->trans('dibs.down.message', [], 'checkout').'</div>';
                }

                return new Response($html, 200, ['Content-Type' => 'text/html']);
            }
        }

        $order    = OrdersPeer::getCurrent();
        $settings = $api->buildFormFields($order);

        if ($isJson) {
            return $this->json_response(['status' => true, 'fields' => $settings]);
        } else {
            return $this->render('PaymentBundle:Dibs:block.html.twig', ['cardtypes' => [], 'form_fields' => $settings]);
        }
    }


    /**
     * Used to see if our migration to the new flow works.
     *
     * @param  Request  $request
     * @param  int      $order_id
     * @return Response
     */
    public function processAction(Request $request, $order_id)
    {
        $order = OrdersPeer::retriveByPaymentGatewayId($order_id);

        if (!empty($order) && ($order->getId() !== $this->get('session')->get('order_id'))) {
            Tools::log('Order id mismatch, in url: '.$order_id. ' in session: '. $request->getSession()->get('order_id').' L: '.$request->getLocale());
        }

        return $this->redirect($this->generateUrl('_checkout_success'));
    }
}
