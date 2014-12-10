<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\PayPal;

use Exception;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\Customers;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;
use Hanzo\Bundle\PaymentBundle\BasePaymentApi;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PayPalApi
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\PayPal
 */
class PayPalApi extends BasePaymentApi implements PaymentMethodApiInterface
{
    /**
     * undocumented class variable
     *
     * @var array
     */
    protected $settings = [];

    protected $router;
    protected $translator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Hanzo\Core\ServiceLogger
     */
    public $service_logger;

    /**
     * __construct
     */
    public function __construct($parameters, $settings)
    {
        $this->router             = $parameters[0];
        $this->translator         = $parameters[1];
        $this->logger             = $parameters[2];
        $this->service_logger     = $parameters[3];
        $this->settings           = $settings;
        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);

        // must explicit be set to "NO" to disable test.
        if (empty($this->settings['test']) || ('NO' !== $this->settings['test'])) {
            $this->settings['test'] = 'YES';
        }

        $env = '';
        if ($this->settings['test'] === 'YES') {
            $env                             = 'sandbox.';
            $this->settings['api_user']      = 'un-facilitator_api1.bellcom.dk';
            $this->settings['api_password']  = '1364979347';
            $this->settings['api_signature'] = 'ABXF9ETaMLWYCEmZokD.mXSrk88hA63P3kKKUzqMvoUft615M4awyDCb';
            $this->settings['api_version']   = '97';
        }

        $this->baseUrl = 'https://api-3t.'.$env.'paypal.com/nvp';
        $this->settings['base_url'] = $this->baseUrl;
        $this->settings['env']      = $env;
    }

    /**
     * call
     * @return boolean
     */
    public function call()
    {
        return PayPalCall::getInstance($this->settings, $this);
    }

    /**
     * cancel
     *
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return PayPalCallResponse
     */
    public function cancel(Customers $customer, Orders $order)
    {
        return new PayPalCallResponse();
    }

    /**
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     */
    public function isActive()
    {
        return (isset($this->settings['active'])) ? $this->settings['active'] : false;
    }

    /**
     * @return bool
     */
    public function isInTestMode()
    {
        return $this->settings['test'] === 'YES';
    }

    /**
     * getFeeExternalId
     * @return mixed
     */
    public function getFeeExternalId()
    {
        return (isset($this->settings['fee.id'])) ? $this->settings['fee.id'] : null;
    }

    /**
     * updateOrderFailed
     *
     * @param Request $request
     * @param Orders  $order
     *
     * @return Orders
     */
    public function updateOrderFailed(Request $request, Orders $order)
    {
        return $this->setOrderState(Orders::STATE_ERROR_PAYMENT, $request, $order);
    }

    /**
     * updateOrderSuccess
     *
     * @param Request $request
     * @param Orders  $order
     *
     * @return Orders
     */
    public function updateOrderSuccess(Request $request, Orders $order)
    {
        return $this->setOrderState(Orders::STATE_PAYMENT_OK, $request, $order);
    }


    /**
     * getProcessButton
     *
     * @param Orders  $order
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function getProcessButton(Orders $order, Request $request)
    {
        $parameters = [
            'email'              => $order->getEmail(),
            'order_id'           => $order->getId(),
            'session_id'         => $order->getSessionId(),
            'payment_gateway_id' => $order->getPaymentGatewayId(),
        ];

        $shipping = 0;
        $shippingLabel = '';
        foreach ($order->getOrderLineShipping() as $line) {
            $shipping += $line->getPrice();
            if ('shipping' === $line->getType()) {
                $shippingLabel = $line->getProductsName();
            }
        }

        $scheme = 'http';
        if (isset($_SERVER['HTTPS']) && ('ON' == strtoupper($_SERVER['HTTPS']))) {
            $scheme = 'https';
        }

        $params = [
            'CANCELURL'          => $this->router->generate('_payment_cancel', $parameters, true),
            'RETURNURL'          => $this->router->generate('_paypal_callback', $parameters, true),
            'LOGOIMG'            => $scheme.'://static.pompdelux.com/fx/images/POMPdeLUX_logo_SS12.png',
            'LOCALECODE'         => $request->getLocale(),
            'BRANDNAME'          => 'POMPdeLUX',
            'NOSHIPPING'         => 1,
            'REQCONFIRMSHIPPING' => 0,
            'ADDROVERRIDE'       => 1,
            'ALLOWNOTE'          => 0,
            'SOLUTIONTYPE'       => 'Sole',
            'LANDINGPAGE'        => 'Login',
        ];
        $params   = $this->paymentDetails($params, $order);
        $response = $this->call()->SetExpressCheckout($params);

        if ($response->isError()) {
            Tools::log(print_r($response->debug(), 1)."\n\nparams send:\n".print_r($params, 1));
            throw new Exception("No PayPal token !!");
        }

        return ['url' => 'https://www.'.$this->settings['env'].'paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$response->getResponseVar('TOKEN')];
    }


    /**
     * @param Request $request
     * @param Orders  $order
     *
     * @return mixed
     * @throws Exception
     */
    public function verifyCallback(Request $request, Orders $order)
    {
        $response = $this->call()->GetExpressCheckoutDetails([
            'TOKEN' => $request->query->get('token'),
        ]);

        if ($response->isError()) {
            Tools::log($response->debug());
            throw new Exception("Payment could not be verified: ".$response->getError());
        }

        return $response;
    }


    /**
     * process the payment
     *
     * @param array  $response
     * @param Orders $order
     *
     * @throws Exception        If DoExpressCheckoutPayment call failes
     * @return boolean
     */
    public function processPayment($response, $order)
    {
        $params = [
            'TOKEN'   => $response->getResponseVar('TOKEN'),
            'PAYERID' => $response->getResponseVar('PAYERID'),
        ];
        $params   = $this->paymentDetails($params, $order);
        $response = $this->call()->DoExpressCheckoutPayment($params);

        if ($response->isError()) {
            Tools::log($response->debug());
            throw new Exception("Payment could not be completed: ".$response->getError());
        }

        foreach ([
            'PAYERID'                     => 'PAYERID',
            'PAYMENTINFO_0_TRANSACTIONID' => 'TRANSACTIONID',
            'PAYMENTINFO_0_PAYMENTSTATUS' => 'PAYMENTSTATUS',
            'PAYMENTINFO_0_PENDINGREASON' => 'PENDINGREASON',
            'CORRELATIONID'               => 'CORRELATIONID',
            'TIMESTAMP'                   => 'TIMESTAMP',
            'TOKEN'                       => 'TOKEN',
            'L_LONGMESSAGE0'              => 'MESSAGE',
        ] as $key => $code) {
            $order->setAttribute($code, 'payment', $response->getResponseVar($key));
        }

        $order->save();

        return true;
    }


    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }


    /**
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }


    /**
     * setOrderState from payment
     *
     * @param Integer $state
     * @param Request $request
     * @param Orders  $order
     *
     * @return Orders
     */
    protected function setOrderState($state, Request $request, Orders $order)
    {
        $order->setState($state);
        $order->setAttribute('paytype', 'payment', 'paypal');
        $order->setAttribute('TOKEN', 'payment', $request->query->get('token'));
        $order->setAttribute('PAYERID', 'payment', $request->query->get('PayerID'));

        return $order->save();
    }


    /**
     * apply payment details to the params array to send to paypal
     *
     * @param array  $params
     * @param Orders $order
     *
     * @return array
     */
    protected function paymentDetails($params, $order)
    {
        $shipping = 0;
        foreach ($order->getOrderLineShipping() as $line) {
            $shipping += $line->getPrice();
        }

        $total = $order->getTotalPrice();
        $params['PAYMENTREQUEST_0_INVNUM']        = $order->getPaymentGatewayId();
        $params['PAYMENTREQUEST_0_AMT']           = number_format($total, 2, '.', '');
        $params['PAYMENTREQUEST_0_ITEMAMT']       = number_format(($total - $shipping), 2, '.', '');
        $params['PAYMENTREQUEST_0_SHIPPINGAMT']   = number_format($shipping, 2, '.', '');
        $params['PAYMENTREQUEST_0_CURRENCYCODE']  = $order->getCurrencyCode();
        $params['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Authorization';

        $i=0;

        $paymentFee = 0;
        foreach ($order->getOrdersLiness() as $line) {
            if ('payment.fee' ===  $line->getType()) {
                $paymentFee += $line->getPrice();
            }

            if ('product' !== $line->getType()) {
                continue;
            }

            // ignore products with 100% discount - paypal cannot handle these...
            if (0.00 == number_format($line->getPrice(), 2, '.', '')) {
                continue;
            }

            $params['L_PAYMENTREQUEST_0_NAME'.$i]         = $line->getProductsName();
            $params['L_PAYMENTREQUEST_0_AMT'.$i]          = number_format($line->getPrice(), 2, '.', '');
            $params['L_PAYMENTREQUEST_0_QTY'.$i]          = $line->getQuantity();
            $params['L_PAYMENTREQUEST_0_ITEMCATEGORY'.$i] = 'Physical';
            $i++;
        }

        foreach ($order->getOrderLineDiscount() as $line) {
            $params['L_PAYMENTREQUEST_0_NAME'.$i]         = $this->translator->trans($line->getProductsSku(), [], 'checkout');
            $params['L_PAYMENTREQUEST_0_AMT'.$i]          = number_format($line->getPrice(), 2, '.', '');
            $params['L_PAYMENTREQUEST_0_QTY'.$i]          = $line->getQuantity();
            $params['L_PAYMENTREQUEST_0_ITEMCATEGORY'.$i] = 'Physical';
            $i++;
        }

        if ($paymentFee > 0) {
            $params['L_PAYMENTREQUEST_0_NAME'.$i]         = $this->translator->trans('payment.fee', [], 'checkout');
            $params['L_PAYMENTREQUEST_0_AMT'.$i]          = number_format($paymentFee, 2, '.', '');
            $params['L_PAYMENTREQUEST_0_QTY'.$i]          = 1;
            $params['L_PAYMENTREQUEST_0_ITEMCATEGORY'.$i] = 'Physical';
            $i++;
        }

        return $params;
    }
}
