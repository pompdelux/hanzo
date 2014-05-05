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

        if (isset($attributes->payment->PAYMENTSTATUS) &&
            ('pending' == strtolower($attributes->payment->PAYMENTSTATUS)) &&
            ('authorization' == strtolower($attributes->payment->PENDINGREASON))
        ) {
            return $this->doDoVoid($attributes->payment->TRANSACTIONID);
        }

        if (empty($attributes->payment->TRANSACTIONID)) {
            \Hanzo\Core\Tools::log(
                'PayPal transaction problems with order: #'.$order->getId()."\n\n".
                print_r($order->toArray(), 1)."\n".
                print_r($attributes, 1)."\n".
                '----------------------------------------------------'
            );
        }

        return $this->doRefundTransaction(
            $attributes->payment->TRANSACTIONID,
            $order->getCustomersId(),
            $order->getPaymentGatewayId()
        );
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
            'AUTHORIZATIONID' => $attributes->payment->TRANSACTIONID,
            'AMT'             => number_format($amount, 2, '.', ''),
            'CURRENCYCODE'    => $order->getCurrencyCode(),
            'COMPLETETYPE'    => 'Complete',
            'INVNUM'          => $order->getPaymentGatewayId(),
        ];

        $response = $this->call('DoCapture', $parameters);

        if (!$response->isError()) {
            foreach ([
                'TIMESTAMP'     => 'CAPTURE_TS',
                'PAYMENTSTATUS' => 'CAPTURE_STATUS',
                'TRANSACTIONID' => 'CAPTURE_TRANSACTIONID',
                'PENDINGREASON' => 'CAPTURE_PENDINGREASON',
            ] as $key => $code) {
                $order->setAttribute($code , 'payment', $response->getResponseVar($key));
            }
            $order->save();
        }

        return $response;
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

        if (empty($attributes->payment->CAPTURE_TRANSACTIONID)) {
            throw new \Exception('Cannot refund orders wich is not captured.');
        }

        $parameters = [
            'TRANSACTIONID' => $attributes->payment->CAPTURE_TRANSACTIONID,
            'PAYERID'       => $attributes->payment->PAYERID,
            'INVOICEID'     => $attributes->payment->TRANSACTIONID,
            'REFUNDTYPE'    => 'Partial',
            'AMT'           => number_format($amount, 2, '.', ''),
            'CURRENCYCODE'  => $order->getCurrencyCode(),
        ];

        $response = $this->call('RefundTransaction', $parameters);

        if (!$response->isError()) {
            foreach ([
                'TIMESTAMP'           => 'REFUND_TS',
                'REFUNDSTATUS'        => 'REFUND_STATUS',
                'REFUNDTRANSACTIONID' => 'REFUND_TRANSACTIONID',
                'PENDINGREASON'       => 'REFUND_PENDINGREASON',
            ] as $key => $code) {
                $order->setAttribute($code , 'payment', $response->getResponseVar($key));
            }
            $order->save();
        }

        return $response;
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

        $query = $this->base_url.'?'.http_build_query($params);

        $logger = $this->api->getLogger();
        $logger->debug('PayPal call to "'.$function.'" send to "'.$this->base_url.'".', $params);

        $response = @file_get_contents($query);

        return new PayPalCallResponse($http_response_header, $response, $function, $logger);
    }


    /**
     * Void pending reservations (orders not processed by AX)
     *
     * @param  string $transaction_id
     * @return PayPalCallResponse
     */
    protected function doDoVoid($transaction_id)
    {
        $parameters = [
            'AUTHORIZATIONID' => $transaction_id,
        ];

        return $this->call('DoVoid', $parameters);
    }


    /**
     * Fully refund an order (orders not processed by AX)
     *
     * @param  string $transaction_id
     * @param  string $customer_id
     * @param  string $payment_gateway_id
     * @return PayPalCallResponse
     */
    protected function doRefundTransaction($transaction_id, $customer_id, $payment_gateway_id)
    {
        $parameters = [
            'TRANSACTIONID' => $transaction_id,
            'PAYERID'       => $customer_id,
            'INVOICEID'     => $payment_gateway_id,
            'REFUNDTYPE'    => 'Full',
        ];

        return $this->call('RefundTransaction', $parameters);
    }
}
