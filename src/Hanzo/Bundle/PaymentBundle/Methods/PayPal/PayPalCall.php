<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\PayPal;

use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

use Hanzo\Core\Hanzo;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiCallInterface;
use Hanzo\Bundle\PaymentBundle\Methods\PayPal\PayPalApi;
use Hanzo\Bundle\PaymentBundle\Methods\PayPal\PayPalCallResponse;

class PayPalCall implements PaymentMethodApiCallInterface
{
    /**
     *
     * @var PayPalCall instance
     */
    private static $instance = null;

    /**
     *
     * @var string
     */
    protected $base_url;

    /**
     *
     * @var array
     */
    protected $settings = array();

    /**
     *
     * @var PayPalApi
     */
    protected $api = null;

    /**
     * __construct
     * @return void
     */
    private function __construct(){}


    /**
     * someFunc
     * @return void
     */
    public static function getInstance(array$settings, PayPalApi $api)
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        self::$instance->api      = $api;
        self::$instance->base_url = $settings['base_url'];
        self::$instance->settings = $settings;

        return self::$instance;
    }


    /**
     * Cancel payment
     *
     * @param  Orders             $order  Order object
     * @return PayPalCallResponse
     */
    public function cancel(Customers $customer, Orders $order)
    {
        $attributes = $order->getAttributes();

        $parameters = [
            'TRANSACTIONID' => $attributes->payment->PAYMENTINFO_0_TRANSACTIONID,
            'PAYERID'       => $order->getCustomersId(),
            'INVOICEID'     => $order->getPaymentGatewayId(),
            'REFUNDTYPE'    => 'Full',
        ];

        return $this->call('RefundTransaction', $parameters);
    }


    /**
     * Capture transaction
     *
     * @param  Orders             $order  Order object
     * @param  int                $amount Amount to capture in order's currency
     * @return PayPalCallResponse
     */
    public function capture(Orders $order, $amount)
    {
        $attributes = $order->getAttributes();

        $parameters = [
            'AUTHORIZATIONID' => $attributes->payment->PAYMENTINFO_0_TRANSACTIONID,
            'AMT'             => number_format($amount, 2, '.', ''),
            'CURRENCYCODE'    => $order->getCurrencyCode(),
            'COMPLETETYPE'    => 'Complete',
            'INVNUM'          => $order->getPaymentGatewayId(),
        ];

        return $this->call('DoCapture', $parameters);
    }


    /**
     * Refund a captured transaction and transfers the money back to the card holders account
     *
     * @param  Orders             $order  Order object
     * @param  int                $amount Amount to refund in order's currency
     * @return PayPalCallResponse
     */
    public function refund(Orders $order, $amount)
    {
        $attributes = $order->getAttributes();

        $parameters = [
            'TRANSACTIONID' => $attributes->payment->PAYMENTINFO_0_TRANSACTIONID,
            'PAYERID'       => $attributes->payment->PAYERID,
            'INVOICEID'     => $order->getPaymentGatewayId(),
            'REFUNDTYPE'    => 'Partial',
            'AMT'           => number_format($amount, 2, '.', ''),
            'CURRENCYCODE'  => $order->getCurrencyCode(),
        ];

        return $this->call('RefundTransaction', $parameters);
    }


    public function SetExpressCheckout($params)
    {
        return $this->call('SetExpressCheckout', $params);
    }

    public function GetExpressCheckoutDetails($params)
    {
        return $this->call('GetExpressCheckoutDetails', $params);
    }

    public function DoExpressCheckoutPayment($params)
    {
        return $this->call('DoExpressCheckoutPayment', $params);
    }


    /**
     * Call wrapper
     *
     * @param  string $function Only the last part of the url, e.g. 'cgi-bin/dostuff.cgi'
     * @param  array  $params   The data that is send to dibs
     * @return PayPalCallResponse
     */
    protected function call($function, array $params)
    {
        $params['USER']      = $this->settings['api_user'];
        $params['PWD']       = $this->settings['api_password'];
        $params['SIGNATURE'] = $this->settings['api_signature'];
        $params['VERSION']   = $this->settings['api_version'];
        $params['METHOD']    = $function;

\Hanzo\Core\Tools::log($params);

        $query = $this->base_url.'?'.http_build_query($params);
        $response = @file_get_contents($query);

        return new PayPalCallResponse($http_response_header, $response, $function);
    }
}
