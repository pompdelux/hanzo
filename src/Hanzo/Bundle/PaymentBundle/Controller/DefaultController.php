<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\OrdersPeer;

class DefaultController extends CoreController
{
    protected $services = [
        'payment.dibsapi'      => 'Dibs',
        'payment.gothiaapi'    => 'Gothia',
        'payment.paybybillapi' => 'PayByBill',
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
        $selected_payment_type = $order->getBillingMethod().':'.$order->getAttributes()->payment->paytype;

        $modules = [];
        foreach ($this->services as $service => $controller) {
            $service = $this->get($service);
            if ($service && $service->isActive()) {

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

        $order->setPaymentMethod( $provider );
        $order->setPaymentPaytype( $method );

        // Handle payment fee
        // Currently hardcoded to 0 vat
        // It also only supports one order line with payment fee, as all others are deleted
        $order->setOrderLinePaymentFee($method, $api->getFee(), 0, $api->getFeeExternalId());
        $order->save();

        $response = array(
            'status' => true,
            'message' => '',
        );

        return $this->json_response($response);
    }


    public function getProcessButtonAction()
    {
        $response = [
            'status'  => false,
            'message' => 'unknown payment method',
        ];

        $order = OrdersPeer::getCurrent();
        $provider = strtolower($order->getBillingMethod());
Tools::log($provider);
        $key = 'payment.'.$provider.'api';

        if (isset($this->services[$key])) {
            $api = $this->get($key);

            $response = [
                'status'  => true,
                'message' => '',
                'data'    => $api->getProcessButton($order),
            ];
        }

        return $this->json_response($response);
    }

}
