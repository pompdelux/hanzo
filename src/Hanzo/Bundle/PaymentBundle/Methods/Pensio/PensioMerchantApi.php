<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use Exception;
use Hanzo\Core\ServiceLogger;
use SimpleXMLElement;
use Hanzo\Model\Customers;
use Hanzo\Model\Orders;
use Hanzo\Bundle\PaymentBundle\PaymentApiCallException;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiCallInterface;

/**
 * Class PensioMerchantApi
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\Pensio
 */
class PensioMerchantApi implements PaymentMethodApiCallInterface
{
    /**
     * gateway url
     * @var string
     */
    protected $baseUrl = 'https://%s.pensio.com';

    /**
     * connection state
     * @var boolean
     */
    protected $connected = false;

    /**
     * api settings
     * @var [type]
     */
    protected $settings = [];

    /**
     * api instance
     * @var PensioMerchantApi
     */
    private static $instance;

    /**
     * @var \Hanzo\Core\ServiceLogger
     */
    private $serviceLogger;

    /**
     * construct
     */
    private function __construct()
    {
    }

    /**
     * factory method
     *
     * @param array                     $settings
     * @param \Hanzo\Core\ServiceLogger $serviceLogger
     *
     * @return PensioMerchantApi
     */
    public static function getInstance(array $settings, $serviceLogger)
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        self::$instance->setup($settings, $serviceLogger);

        return self::$instance;
    }

    /**
     * setup object
     *
     * @param array         $settings
     * @param ServiceLogger $serviceLogger
     */
    protected function setup(array $settings, ServiceLogger $serviceLogger)
    {
        $this->settings      = $settings;
        $this->serviceLogger = $serviceLogger;
        $this->baseUrl       = sprintf($this->baseUrl, $this->settings['gateway']);
        $this->connect();
    }

    /**
     * capture order amount
     *
     * @param Orders $order
     * @param float  $amount
     *
     * @return PensioCallResponse
     * @throws PaymentApiCallException
     */
    public function capture(Orders $order, $amount)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api capture action: order contains no transaction id, order id was: '.$order->getId());
        }

        return $this->callAPIMethod(
            'captureReservation', [
                'transaction_id' => $attributes->payment->transaction_id,
                'amount' => number_format($amount, 2, '.', '')
            ]
        );
    }

    /**
     * refund order amount
     *
     * @param Orders $order
     * @param float  $amount
     *
     * @return PensioCallResponse|false
     * @throws PaymentApiCallException
     */
    public function refund(Orders $order, $amount = 0.0)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api refund action: order contains no transaction id, order id was: '.$order->getId());
        }

        $params = ['transaction_id' => $attributes->payment->transaction_id];

        if (0 != $amount) {
            $params['amount'] = number_format($amount, 2, '.', '');
        }

        return $this->callAPIMethod(
            'refundCapturedReservation', $params
        );
    }

    /**
     * cancel payment
     *
     * @param Customers $customer
     * @param Orders    $order
     *
     * @return PensioCallResponse|false
     * @throws PaymentApiCallException
     */
    public function cancel(Customers $customer, Orders $order)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        // If the payment type does not support release requests, we refund the total amount instead.
        if (isset($attributes->payment->SupportsRelease)) {
            if ((0 == $attributes->payment->SupportsRelease) &&
                (1 == $attributes->payment->SupportsRefunds)
            ) {
                return $this->refund($order);
            }
        } else {
            // TODO: legacy support - should be removed after SS2014
            if (($attributes->payment->nature == 'IdealPayment') &&
                ($attributes->payment->type == 'paymentAndCapture')
            ) {
                return $this->refund($order);
            }
        }

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api cancel action: order contains no transaction id, order id was: '.$order->getId());
        }

        return $this->callAPIMethod(
            'releaseReservation', [
                'transaction_id' => $attributes->payment->transaction_id
            ]
        );
    }

    /**
     * get a payment object
     *
     * @param Orders  $order               Orders object
     * @param Boolean $usePaymentGatewayId If set to true, we use the payment_gateway_id for lookups, not the transaction id
     *
     * @return mixed
     * @throws PaymentApiCallException
     */
    public function getPayment(Orders $order, $usePaymentGatewayId = false)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        if ($usePaymentGatewayId) {
            return $this->callAPIMethod(
                'payments', [
                    'shop_orderid' => $order->getPaymentGatewayId()
                ]
            );
        }

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api cancel action: order contains no transaction id, order id was: '.$order->getId());
        }

        $body = $this->callAPIMethod(
            'payments', [
                'transaction' => $attributes->payment->transaction_id
            ]
        );

        if (isset($body->Transactions[0])) {
            return $body->Transactions[0]->Transaction[0];
        }

        return null;
    }

    /**
     * connect, aka setup parameters used and test connection
     *
     * @return void
     * @throws Exception If test call failed
     */
    protected function connect()
    {
        if ($this->connected) {
            return;
        }

        $this->connected = false;

        // Just any method.
        if (false === $this->callAPIMethod('fundingList')) {
            throw new Exception("Connection failed, make sure username and password are correct, and you have the 'api' credential");
        } else {
            $this->connected = true;
        }
    }

    /**
     * connection check
     *
     * @throws Exception on error
     */
    protected function checkConnection()
    {
        if (!$this->connected) {
            throw new Exception("Not Connected, invoke connect(...) before using any API calls");
        }
    }

    /**
     * build the stream context
     *
     * @param array $args
     *
     * @return resource
     */
    protected function createContext(array $args)
    {
        $headers = [
            'Authorization: Basic '.base64_encode($this->settings['api_user'].':'.$this->settings['api_pass']),
            'Content-type: application/x-www-form-urlencoded',
        ];

        return stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'timeout' => 5,
                'ignore_errors' => false,
                'content' => http_build_query($args),
            ],
        ]);
    }

    /**
     * perform the call agianst pensio
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function callAPIMethod($method, array $args = [])
    {
        $result = @file_get_contents($this->baseUrl."/merchant/API/".$method, false, $this->createContext($args));

        $this->serviceLogger->plog($args, ['outgoing', 'payment', 'pensio', $method]);

        if ($result !== false) {
            return new PensioCallResponse($http_response_header, new SimpleXMLElement($result));
        }

        return false;
    }
}
