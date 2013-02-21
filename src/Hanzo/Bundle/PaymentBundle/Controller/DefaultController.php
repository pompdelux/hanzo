<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Bundle\CheckoutBundle\Controller\DefaultController AS CheckoutController;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;

class DefaultController extends CoreController
{
    protected $services = [
        'payment.dibsapi'      => 'Dibs',
        'payment.gothiaapi'    => 'Gothia',
        'payment.paybybillapi' => 'PayByBill',
        'payment.couponapi'    => 'Coupon',  // pseudo payment module ...
        'payment.pensioapi'    => 'Pensio',
    ];

    /**
     * blockAction
     *
     * @return object Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $order = OrdersPeer::getCurrent();

        $selected_payment_type = '';
        if ($order->getBillingMethod()) {
            $selected_payment_type = $order->getBillingMethod().':'.$order->getAttributes()->payment->paytype;
        }

        $redis = $this->get('redis.permanent');
        $dibs_status = $redis->hget('service.status', 'dibs');


        $modules = [];
        foreach ($this->services as $service => $controller) {

            $service = $this->get($service);
            if ($service && $service->isActive()) {

                if ('Dibs' == $controller && 'DOWN' === $dibs_status) {
                    $modules[] = '<div class="down">'.$this->get('translator')->trans('dibs.down.message', [], 'checkout').'</div>';
                    continue;
                }

                $parameters = [
                    'order' => $order,
                    'selected_payment_type' => $selected_payment_type,
                ];

                // TODO: fix hardcoded "cardtypes"
                if (method_exists($service, 'getEnabledPaytypes')) {
                    $parameters['cardtypes'] = $service->getEnabledPaytypes();
                }

                $modules[] = $this->render('PaymentBundle:'.$controller.':select.html.twig', $parameters)->getContent();
            }
        }

        return $this->render('PaymentBundle:Default:block.html.twig', [
            'modules' => $modules,
            'selected_payment_type' => $selected_payment_type,
        ]);
    }


    public function setMethodAction(Request $request)
    {
        $response = array(
            'status' => false,
            'message' => 'unknown payment method',
        );

        list($provider, $method) = explode(':', $request->get('method'));

        $key = 'payment.'.$provider.'api';

        if (isset($this->services[$key])) {
            $api = $this->get($key);
        } else {
            return $this->json_response($response);
        }

        $order = OrdersPeer::getCurrent();

        if ($order->getState() >= Orders::STATE_PRE_PAYMENT) {
            $trans = $this->get('translator');
            $response['message'] = $trans->trans('order.state_pre_payment.locked', [], 'checkout');

            return $this->json_response($response);
        }


        $order->setPaymentMethod( $provider );
        $order->setPaymentPaytype( $method );

        // Handle payment fee
        // Currently hardcoded to 0 vat
        // It also only supports one order line with payment fee, as all others are deleted

        if ('DOWN' !== $this->get('redis.permanent')->hget('service.status', 'dibs')) {
            $order->setOrderLinePaymentFee($method, $api->getFee(), 0, $api->getFeeExternalId());
        }

        $order->setUpdatedAt(time());
        $order->save();

        $response = [
            'status'  => true,
            'message' => '',
        ];

        return $this->json_response($response);
    }


    public function getProcessButtonAction()
    {
        $response = [
            'status'  => false,
            'message' => 'unknown payment method',
        ];

        $order = OrdersPeer::getCurrent();

        // validation

        if ($order->getState() >= Orders::STATE_PRE_PAYMENT) {
            return $this->json_response([
                'status'  => false,
                'message' => $this->get('translator')->trans('order.state_pre_payment.locked', [], 'checkout'),
                'data'    => ['name' => 'payment'],
            ]);
        }

        $provider = strtolower($order->getBillingMethod());
        $key = 'payment.'.$provider.'api';

        if (isset($this->services[$key])) {
            $api = $this->get($key);

            $response = [
                'status'  => true,
                'message' => '',
                'data'    => $api->getProcessButton($order),
            ];
        }

        // If the customer cancels payment, state is back to building
        // Customer is only allowed to add products to the basket if state is >= pre payment
        $order->setState( Orders::STATE_PRE_PAYMENT );
        $order->save();

        return $this->json_response($response);
    }


    /**
     * Cancels a Gothia Payment, and restores the order in good state
     *
     * @return void
     **/
    public function cancelAction()
    {
        $translator = $this->get('translator');

        $order = OrdersPeer::getCurrent();
        $order->setState( Orders::STATE_BUILDING );
        $order->save();

        $this->get('session')->setFlash('notice', $translator->trans( 'payment.canceled', array(), 'checkout' ));

        return $this->redirect($this->generateUrl('_checkout'));
    }
}
