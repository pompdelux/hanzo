<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Hanzo\Core\CoreController;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;

class DefaultController extends CoreController
{
    protected $services = [
        'payment.dibsapi'          => 'Dibs',
        'payment.gothiaapi'        => 'Gothia',
        'payment.gothiadeapi'      => 'GothiaDE',
        'payment.manualpaymentapi' => 'ManualPayment',
        'payment.giftcardapi'      => 'GiftCard',
        'payment.pensioapi'        => 'Pensio',
        'payment.paypalapi'        => 'PayPal',
    ];

    /**
     * blockAction
     *
     * @return object Response
     */
    public function blockAction()
    {
        $order = OrdersPeer::getCurrent();

        $selectedPaymentType = '';
        if ($order->getBillingMethod()) {
            $selectedPaymentType = $order->getBillingMethod().':'.$order->getAttributes()->payment->paytype;
        }

        $redis = $this->get('pdl.phpredis.permanent');
        $dibsStatus = $redis->hget('service.status', 'dibs');

        $modules = [];
        foreach ($this->services as $service => $controller) {
            $service = $this->get($service);
            if ($service && $service->isActive()) {
                if (('Dibs' == $controller) && ('DOWN' === $dibsStatus)) {
                    $modules[] = '<div class="down">'.$this->get('translator')->trans('dibs.down.message', [], 'checkout').'</div>';
                    continue;
                }

                $parameters = [
                    'order'                 => $order,
                    'cardtypes'             => $service->getPayTypes(),
                    'selected_payment_type' => $selectedPaymentType,
                ];

                $paymentHtml = $this->render('PaymentBundle:'.$controller.':select.html.twig', $parameters)->getContent();

                // If the service has an specific order. Add it to that index.
                if ($service->getOrder()) {
                    $modules[$service->getOrder()] = $paymentHtml;
                } else {
                    $modules[$controller] = $paymentHtml;
                }
            }
        }

        // Order the modules by index, and reverse them so the highest index get in top.
        ksort($modules);
        $modules = array_reverse($modules);

        return $this->render('PaymentBundle:Default:block.html.twig', [
            'modules'               => $modules,
            'selected_payment_type' => $selectedPaymentType,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function setMethodAction(Request $request)
    {
        $response = [
            'status'  => false,
            'message' => 'unknown payment method',
        ];

        list($provider, $method) = explode(':', $request->request->get('method'));

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

        $order->setPaymentMethod($provider);
        $order->setPaymentPaytype($method);

        // Handle payment fee
        // Currently hardcoded to 0 vat
        // It also only supports one order line with payment fee, as all others are deleted

        if ('DOWN' !== $this->get('pdl.phpredis.permanent')->hget('service.status', 'dibs')) {
            $order->setPaymentFee($method, $api->getFee($method), 0, $api->getFeeExternalId());
        }

        $order->setUpdatedAt(time());
        $order->save();

        $response = [
            'status'  => true,
            'message' => '',
        ];

        return $this->json_response($response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function getProcessButtonAction(Request $request)
    {
        $response = [
            'status'  => false,
            'message' => 'unknown payment method',
        ];

        $order = OrdersPeer::getCurrent();
        $order->reload();
        $attributes = $order->getAttributes();

        // validation

        // if the order has validation issues, we just return false to halt the checkout process
        if (isset($attributes->global->not_valid)) {
            return $this->json_response([
                'status'  => false,
                'message' => '',
            ]);
        }

        if ($order->getState() >= Orders::STATE_PRE_PAYMENT) {
            return $this->json_response([
                'status'  => false,
                'message' => $this->get('translator')->trans('order.state_pre_payment.locked', [], 'checkout'),
                'data'    => ['name' => 'payment'],
            ]);
        }

        $provider = strtolower($order->getBillingMethod());
        $key      = 'payment.'.$provider.'api';

        if (isset($this->services[$key])) {
            $api = $this->get($key);

            $response = [
                'status'  => true,
                'message' => '',
                'data'    => $api->getProcessButton($order, $request),
            ];
        }

        // If the customer cancels payment, state is back to building
        // Customer is only allowed to add products to the basket if state is >= pre payment
        $order->setState(Orders::STATE_PRE_PAYMENT);
        $order->save();

        return $this->json_response($response);
    }

    /**
     * Cancels a Gothia Payment, and restores the order in good state
     *
     * @return Response
     */
    public function cancelAction()
    {
        $translator = $this->get('translator');

        $order = OrdersPeer::getCurrent();
        $order->setState(Orders::STATE_BUILDING);
        $order->save();

        $this->get('session')->getFlashBag()->add('notice', $translator->trans('payment.canceled', [], 'checkout'));

        return $this->redirect($this->generateUrl('_checkout'));
    }
}
