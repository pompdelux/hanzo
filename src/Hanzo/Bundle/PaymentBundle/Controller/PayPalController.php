<?php /* vim: set sw=4: */

/**
 * Username.: un-facilitator_api1.bellcom.dk
 * Password.: 1364979347
 * Signature: ABXF9ETaMLWYCEmZokD.mXSrk88hA63P3kKKUzqMvoUft615M4awyDCb
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

use Hanzo\Bundle\PaymentBundle\Methods\PayPal\PayPalApi;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class PayPalController extends CoreController
{
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
        $order = OrdersQuery::create()
            ->findOneByPaymentGatewayId(
                $request->query->get('payment_gateway_id'),
                Propel::getConnection(null, Propel::CONNECTION_WRITE)
            )
        ;

        $flash_bag = $this->get('session')->getFlashBag();

        if ($order instanceof Orders) {
            $order->reload(true);
            $api = $this->get('payment.paypalapi');

            try {
                if ($response = $api->verifyCallback($request, $order)) {
                    $api->processPayment($response, $order);
                }

                $api->updateOrderSuccess($request, $order);
                $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
                $status = 'ok';
            } catch (Exception $e) {
                $status = 'failed';
                $message = $e->getMessage();
                Tools::log($e->getMessage());
            }

            if ('ok' === $status) {
                return $this->redirect($this->generateUrl('_checkout_success'));
            }

            $api->updateOrderFailed($request, $order);

            $flash_bag->add('notice', $this->get('translator')->trans($message, [], 'paypal'));
        } else {
            $flash_bag->add('notice', $this->get('translator')->trans('order.not.found', [], 'paypal'));
        }

        return $this->redirect($this->generateUrl('_checkout'));
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
