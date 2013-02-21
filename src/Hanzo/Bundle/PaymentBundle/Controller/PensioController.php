<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersPeer;

use Hanzo\Bundle\PaymentBundle\Methods\Pensio\PensioApi;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class PensioController extends CoreController
{

    /**
     * the form template to hand off to Pensio
     *
     * @Template("PaymentBundle:Pensio:form")
     * @param  Request $request
     * @return Response
     */
    public function formAction(Request $request)
    {
        if ('77.66.40.133' !== $request->getClientIp()) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()
            ->findOneByPaymentGatewayId($request->get('shop_orderid'))
        ;

        return [
            'order_id' => $order->getId(),
            'amount' => $order->getTotalPrice()
        ];
    }


    /**
     * redirect page
     *
     * @Template("PaymentBundle:Pensio:wait")
     * @param  Request $request
     * @return Response
     */
    public function waitAction(Request $request)
    {
        if ('77.66.40.133' !== $request->getClientIp()) {
            throw new AccessDeniedException();
        }

        return [];
    }


    /**
     * callbackAction
     * post params [
     *     status
     *     error_message
     *     merchant_error_message
     *     shop_orderid
     *     transaction_id
     *     type
     *     payment_status
     *     masked_credit_card
     *     blacklist_token
     *     credit_card_token
     *     nature
     *     require_capture
     *     xml
     *     checksum
     * ]
     *
     * @return void
     */
    public function callbackAction(Request $request, $status = 'failed')
    {
        if ('77.66.40.133' !== $request->getClientIp()) {
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()
            ->findOneByPaymentGatewayId($request->get('shop_orderid'))
        ;

        if ($order instanceof Orders) {

            $api = $this->get('payment.pensioapi');

            try {
                $api->verifyCallback($request, $order);
                $api->updateOrderSatus($request, $order, true);

                $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
            } catch (Exception $e) {
                $status = 'failed';
                Tools::log($e->getMessage());
            }

            if ('ok' === $status) {
                return $this->redirect($this->generateUrl('_checkout_success'));
            }

            $api->updateOrderSatus($request, $order, false);
        }

        return $this->render('PaymentBundle:Pensio:failed.html.twig', [
            'message'    => $request->get('error_message'),
            'order_id'   => $order->getId(),
            'amount'     => $order->getTotalPrice(),
            'payment_id' => $order->getPaymentGatewayId()
        ]);
    }


    public function processAction(Request $request){}


    /**
     * cancelAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancelAction(Request $request)
    {
        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }
}
