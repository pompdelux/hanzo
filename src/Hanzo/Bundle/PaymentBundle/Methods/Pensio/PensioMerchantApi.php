<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use Exception;
use SimpleXMLElement;

use Hanzo\Core\Tools;
use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

use Hanzo\Bundle\PaymentBundle\PaymentApiCallException;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiCallInterface;

class PensioMerchantApi implements PaymentMethodApiCallInterface
{
    /**
     * gateway url
     * @var string
     */
    protected $base_url = 'https://%s.pensio.com';

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
     * construct
     */
    private function __construct() {}


    /**
     * factory method
     *
     * @param  array $settings
     * @return PensioMerchantApi
     */
    public static function getInstance(array $settings)
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        self::$instance->setup($settings);
        return self::$instance;
    }


    /**
     * capture order amount
     *
     * @param  Orders $order
     * @param  float $amount
     * @return PensioCallResponse
     */
    public function capture(Orders $order, $amount)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api capture action: order contains no transaction id, order id was: '.$order->getId() );
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
     * @param  Orders $order
     * @param  float $amount
     * @return PensioCallResponse
     */
    public function refund(Orders $order, $amount)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api refund action: order contains no transaction id, order id was: '.$order->getId() );
        }

        return $this->callAPIMethod(
            'refundCapturedReservation', [
                'transaction_id' => $attributes->payment->transaction_id,
                'amount' => number_format($amount, 2, '.', '')
            ]
        );
    }


    /**
     * cancel payment
     *
     * @param  Customers $customer
     * @param  Orders    $order
     * @return PensioCallResponse
     */
    public function cancel(Customers $customer, Orders $order)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api cancel action: order contains no transaction id, order id was: '.$order->getId() );
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
     * @param  Orders $order
     * @return mixed
     */
    public function getPayment(Orders $order)
    {
        $this->checkConnection();

        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transaction_id)) {
            throw new PaymentApiCallException('Pensio api cancel action: order contains no transaction id, order id was: '.$order->getId() );
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
     * setup object
     *
     * @param  array  $settings
     */
    protected function setup(array $settings)
    {
        $this->settings = $settings;
        $this->base_url = sprintf($this->base_url, $this->settings['gateway']);
        $this->connect();
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
     * @param  array  $args
     * @return ressource
     */
    protected function createContext(array $args)
    {
        $headers = [
            'Authorization: Basic '.base64_encode($this->settings['api_user'].':'.$this->settings['api_pass']),
            'Content-type: application/x-www-form-urlencoded'
        ];

        return stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'timeout' => 5,
                'ignore_errors' => false ,
                'content' => http_build_query($args),
            ]
        ]);
    }


    /**
     * perform the call agianst pensio
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    protected function callAPIMethod($method, array $args = [])
    {
        $context = $this->createContext($args);
        $result = @file_get_contents($this->base_url."/merchant/API/".$method, false , $context);

        if ($result !== false) {
            return new PensioCallResponse($http_response_header, new SimpleXMLElement($result));
        }

        return false;
    }
}
