<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\PayPal;

use Hanzo\Bundle\PaymentBundle\PaymentMethodApiCallInterface;
use Hanzo\Core\Tools;
use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

/**
 * Class PayPalCall
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\PayPal
 */
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
    protected $baseUrl;

    /**
     *
     * @var array
     */
    protected $settings = [];

    /**
     *
     * @var PayPalApi
     */
    protected $api = null;

    /**
     * Constructor
     */
    private function __construct()
    {
    }


    /**
     * Factory method
     *
     * @param array     $settings
     * @param PayPalApi $api
     *
     * @return self
     */
    public static function getInstance(array $settings, PayPalApi $api)
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        self::$instance->api      = $api;
        self::$instance->baseUrl  = $settings['base_url'];
        self::$instance->settings = $settings;

        return self::$instance;
    }


    /**
     * Cancel payment
     *
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return PayPalCallResponse|PayPallDummyCallResponse
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
            if (1 == $order->getVersionId()) {
                return new PayPallDummyCallResponse();
            }

            Tools::log(
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
     * @param Orders $order  Order object
     * @param int    $amount Amount to capture in order's currency
     *
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
                $order->setAttribute($code, 'payment', $response->getResponseVar($key));
            }
            $order->save();
        }

        return $response;
    }


    /**
     * Refund a captured transaction and transfers the money back to the card holders account
     *
     * @param Orders $order  Order object
     * @param int    $amount Amount to refund in order's currency
     *
     * @return PayPalCallResponse
     * @throws \Exception
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
                $order->setAttribute($code, 'payment', $response->getResponseVar($key));
            }
            $order->save();
        }

        return $response;
    }

    /**
     * @param array $params
     *
     * @return PayPalCallResponse
     */
    public function SetExpressCheckout($params)
    {
        return $this->call('SetExpressCheckout', $params);
    }

    /**
     * @param array $params
     *
     * @return PayPalCallResponse
     */
    public function GetExpressCheckoutDetails($params)
    {
        return $this->call('GetExpressCheckoutDetails', $params);
    }

    /**
     * @param array $params
     *
     * @return PayPalCallResponse
     */
    public function DoExpressCheckoutPayment($params)
    {
        return $this->call('DoExpressCheckoutPayment', $params);
    }


    /**
     * Call wrapper
     *
     * @param string $function Only the last part of the url, e.g. 'cgi-bin/dostuff.cgi'
     * @param array  $params   The data that is send to dibs
     *
     * @return PayPalCallResponse
     */
    protected function call($function, array $params)
    {
        $params['USER']      = $this->settings['api_user'];
        $params['PWD']       = $this->settings['api_password'];
        $params['SIGNATURE'] = $this->settings['api_signature'];
        $params['VERSION']   = $this->settings['api_version'];
        $params['METHOD']    = $function;

        $query = $this->baseUrl.'?'.http_build_query($params);

        $logger = $this->api->getLogger();
        $logger->debug('PayPal call to "'.$function.'" send to "'.$this->baseUrl.'".', $params);

        $this->api->service_logger->plog($query, ['outgoing', 'payment', 'paypal', $function]);

        $response = @file_get_contents($query);

        return new PayPalCallResponse($http_response_header, $response, $function, $logger);
    }


    /**
     * Void pending reservations (orders not processed by AX)
     *
     * @param string $transactionId
     *
     * @return PayPalCallResponse
     */
    protected function doDoVoid($transactionId)
    {
        return $this->call('DoVoid', ['AUTHORIZATIONID' => $transactionId]);
    }


    /**
     * Fully refund an order (orders not processed by AX)
     *
     * @param string $transactionId
     * @param string $customerId
     * @param string $paymentGatewayId
     *
     * @return PayPalCallResponse
     */
    protected function doRefundTransaction($transactionId, $customerId, $paymentGatewayId)
    {
        return $this->call('RefundTransaction', [
            'TRANSACTIONID' => $transactionId,
            'PAYERID'       => $customerId,
            'INVOICEID'     => $paymentGatewayId,
            'REFUNDTYPE'    => 'Full',
        ]);
    }
}
