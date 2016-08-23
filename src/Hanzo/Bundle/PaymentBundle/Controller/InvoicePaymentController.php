<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Hanzo\Bundle\PaymentBundle\Methods\InvoicePayment\InvoicePaymentApi;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class InvoicePaymentController
 *
 * @package Hanzo\Bundle\PaymentBundle
 */
class InvoicePaymentController extends CoreController
{
    /**
     * callbackAction
     *
     * @param Request $request
     *
     * @throws Exception
     * @return Response
     */
    public function callbackAction(Request $request)
    {
        $api   = $this->get('payment.invoicepaymentapi');
        $order = OrdersPeer::getCurrent(true);

        /**
         * used in google analytics to generate stats on order order edits.
         */
        $queryParameters = [];
        if ($order->getInEdit()) {
            $queryParameters = ['is-edit' => 1];
        }

        if ( !($order instanceof Orders) ) {
            throw new Exception('InvoicePayment callback found no valid order to proccess.');
        }

        try {
            $api->updateOrderSuccess($request, $order);

            /**
             * Listeners includes:
             *  - stopping order edit flows
             *  - cansellation of "old" payments (for edits)
             *  - adding the order to beanstalk for processing
             *  - ..
             */
            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
        } catch (Exception $e) {
            Tools::log($e->getMessage());
        }

        return $this->redirect($this->generateUrl('_checkout_success', $queryParameters));
    }


    /**
     * blockAction
     * @return Response
     */
    public function blockAction()
    {
        $api = $this->get('payment.invoicepaymentapi');

        if (!$api->isActive()) {
            return new Response('', 200, ['Content-Type' => 'text/html']);
        }

        return $this->render('PaymentBundle:InvoicePayment:block.html.twig');
    }
}
