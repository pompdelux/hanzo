<?php /* vim: set sw=4: */

/**
 * Terminal: Pomp De Lux iDEAL Test Terminal
 * Bruger..: un@bellcom.dk
 * Password: y2etx3@vz5Jc
 */


namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;
use Propel;

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
     * @Template("PaymentBundle:Pensio:form.html.twig")
     * @param  Request $request
     * @return Response
     * @throws AccessDeniedException
     */
    public function formAction(Request $request)
    {
        if (!in_array($request->getClientIp(), ['77.66.40.133', '127.0.0.1'])) {
            Tools::log('Access denied for '.$request->getClientIp().' to '.__METHOD__);
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()
            ->findOneByPaymentGatewayId(
                $request->get('shop_orderid'),
                Propel::getConnection(null, Propel::CONNECTION_WRITE)
            )
        ;

        return [
            'order_id' => $order->getId(),
            'payment_id' => $order->getPaymentGatewayId(),
            'amount' => $order->getTotalPrice()
        ];
    }


    /**
     * redirect page
     *
     * @Template("PaymentBundle:Pensio:wait.html.twig")
     * @param  Request $request
     * @return Response
     * @throws AccessDeniedException
     */
    public function waitAction(Request $request)
    {
        if (!in_array($request->getClientIp(), ['77.66.40.133', '127.0.0.1'])) {
            Tools::log('Access denied for '.$request->getClientIp().' to '.__METHOD__);
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
     * @param Request $request
     * @param string  $status
     * @return Response
     * @throws AccessDeniedException
     */
    public function callbackAction(Request $request, $status = 'failed')
    {
        if (!in_array($request->getClientIp(), ['77.66.40.133', '127.0.0.1'])) {
            Tools::log('Access denied for '.$request->getClientIp().' to '.__METHOD__);
            throw new AccessDeniedException();
        }

        $order = OrdersQuery::create()
            ->findOneByPaymentGatewayId(
                $request->get('shop_orderid'),
                Propel::getConnection(null, Propel::CONNECTION_WRITE)
            )
        ;
        $order->reload(true);

        if ($order instanceof Orders) {
            $api = $this->get('payment.pensioapi');

            try {
                $api->verifyCallback($request, $order);
                $api->updateOrderStatus(Orders::STATE_PAYMENT_OK, $request, $order);

                $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
            } catch (Exception $e) {
                $status = 'failed';
                Tools::log($e->getMessage());
            }

            if ('ok' === $status) {
                // pensio fails when returning a body in the redirect, so we custom build the response header and exit
                header('Location: '.$this->generateUrl('_checkout_success', [], true), 302);
                exit;
            }

            $api->updateOrderStatus(Orders::STATE_ERROR_PAYMENT, $request, $order);

            return $this->render('PaymentBundle:Pensio:failed.html.twig', [
                'message'         => $request->get('error_message'),
                'order_id'        => $order->getId(),
                'amount'          => $order->getTotalPrice(),
                'payment_id'      => $order->getPaymentGatewayId(),
                'back_url'        => $this->generateUrl('_payment_cancel', [], true),
                'skip_my_account' => true,
            ]);
        }

        return new Response('FAILED', 500, array('Content-Type' => 'text/plain'));
    }

    public function processAction(Request $request){}


    /**
     * cancelAction
     * @param  Request $request
     * @return Response
     **/
    public function cancelAction(Request $request)
    {
        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }


    /**
     * Get transaction information on a order id
     *
     * @param  Request $request
     * @param  Integer $order_id
     * @return Response
     */
    public function lookupAction(Request $request, $order_id)
    {
        $order = OrdersQuery::create()
            ->findOneById(
                $order_id, // 19653
                Propel::getConnection(null, Propel::CONNECTION_WRITE)
            )
        ;

        $api = $this->get('payment.pensioapi');
        $result = $api->call()->getPayment($order, true);

        return new Response('<pre>'.print_r($result,1).'</pre>', 200, array('Content-Type' => 'text/plain'));
    }
}
