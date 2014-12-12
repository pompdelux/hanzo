<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Bundle\PaymentBundle\Methods\GiftCard\GiftCardApi;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class GiftCardController extends CoreController
{
    /**
     * callbackAction
     * @return Response
     * @throws Exception
     **/
    public function callbackAction()
    {
        $api = $this->get('payment.giftcardapi');
        $request = $this->get('request');
        $order = OrdersPeer::getCurrent(true);

        if (!($order instanceof Orders)) {
            throw new Exception('GiftCard callback found no valid order to proccess.');
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

        return $this->redirect($this->generateUrl('_checkout_success'));
    }

    /**
     * cancelAction
     * @return Response
     **/
    public function cancelAction()
    {
        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }


    /**
     * blockAction
     * @return Response
     **/
    public function blockAction()
    {
        $api = $this->get('payment.giftcardapi');

        if (!$api->isActive()) {
            return new Response('', 200, array('Content-Type' => 'text/html'));
        }

        return $this->render('PaymentBundle:GiftCard:block.html.twig');
    }
}
