<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Dibs;

use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

use Hanzo\Bundle\PaymentBundle\PaymentMethodApiCallInterface;

class DibsApiCall implements PaymentMethodApiCallInterface
{
    /**
     * @var bool
     **/
    const USE_AUTH_HEADERS = true;

    /**
     * @var DibsApiCall instance
     **/
    private static $instance = null;

    /**
     * @var string
     **/
    protected $baseUrl = 'https://payment.architrade.com/';

    /**
     * @var array
     **/
    protected $settings = array();

    /**
     * @var \Hanzo\Bundle\PaymentBundle\Methods\Dibs\Type\FlexWin|\Hanzo\Bundle\PaymentBundle\Methods\Dibs\Type\DibsPaymentWindow
     **/
    protected $api = null;

    /**
     * __construct
     **/
    private function __construct() {}

    /**
     * someFunc
     *
     * @param array $settings
     * @param DibsApi
     * @return self
     **/
    public static function getInstance(array $settings, $api)
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        self::$instance->settings = $settings;
        self::$instance->api      = $api;

        return self::$instance;
    }

    /**
     * Curl wrapper
     *
     * @param string $function Only the last part of the url, e.g. 'cgi-bin/dostuff.cgi'
     * @param array $params The data that is send to dibs
     * @param bool $useAuthHeaders Should extra authorization headers be send in request
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     **/
    protected function call($function, array $params, $useAuthHeaders = false)
    {
        $ch = curl_init();

        $url = $this->baseUrl . $function;

        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        if ($useAuthHeaders) {
            if (!isset($this->settings['api_user']) || !isset($this->settings['api_pass'])) {
                throw new DibsApiCallException('DIBS api: Missing api username or/and password');
            }

            $headers = [
                'Authorization: Basic '. base64_encode($this->settings['api_user'].':'.$this->settings['api_pass'])
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
        }

        $response = curl_exec($ch);

        curl_close($ch);

        $this->api->service_logger->plog($params, ['outgoing', 'payment', 'dibs', $function]);

        if ($response === false) {
            throw new DibsApiCallException('Kommunikation med DIBS fejlede, fejlen var: "'.curl_error($ch).'"');
        }

        return new DibsApiCallResponse($response, $function);
    }

    /**
     * Query dibs for status om acquirers
     *
     * @param string $acquirer
     * @return DibsApiCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function status($acquirer = 'all')
    {
        $params = [
            'replytype' => 'html',
            'acquirer'  => $acquirer,
        ];

        return $this->call('status.pml', $params);
    }

    /**
     * Cancel payment
     * http://tech.dibs.dk/dibs-api/payment-functions/cancelcgi/
     *
     * @param Customers $customer
     * @param Orders $order
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     **/
    public function cancel(Customers $customer, Orders $order)
    {
        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException('DIBS api cancel action: order contains no transaction id, order id was: '.$order->getId());
        }

        $transaction      = $attributes->payment->transact;
        $paymentGatewayId = $order->getPaymentGatewayId();
        $stringToHash     = 'merchant=' . $this->settings['merchant'] . '&orderid=' . $paymentGatewayId . '&transact=' . $transaction;

        $params = [
            'merchant'  => $this->settings['merchant'],
            'transact'  => $transaction,
            'textreply' => 'true',
            'md5key'    => $this->api->md5keyFromString( $stringToHash ),
            'orderid'   => $paymentGatewayId,
        ];

        return $this->call('cgi-adm/cancel.cgi', $params, self::USE_AUTH_HEADERS );
    }

    /**
     * Capture transaction
     * http://tech.dibs.dk/dibs-api/payment-functions/capturecgi/
     *
     * @param Orders $order
     * @param int $amount Should be in smallest format, e.g. if you want to capture 175,50 the number should be 17550 (remember the last zero)
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     */
    public function capture(Orders $order, $amount)
    {
        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException( 'DIBS api capture action: order contains no transaction id, order id was: '.$order->getId() );
        }

        $transaction      = $attributes->payment->transact;
        $paymentGatewayId = $order->getPaymentGatewayId();
        $amount           = $this->api->formatAmount($amount);
        $stringToHash     = 'merchant='. $this->settings['merchant'] .'&orderid='. $paymentGatewayId.'&transact='.$transaction.'&amount='.$amount;

        $params = [
            'amount'    => $amount,
            'merchant'  => $this->settings['merchant'],
            'transact'  => $transaction,
            'orderid'   => $paymentGatewayId,
            'textreply' => 'true',
            'md5key'    => $this->api->md5keyFromString($stringToHash),
        ];

        return $this->call('cgi-bin/capture.cgi', $params);
    }

    /**
     * Cancels a captured transaction and transfers the money back to the card holders account
     * http://tech.dibs.dk/dibs-api/payment-functions/refundcgi/
     *
     * @param Orders $order
     * @param int $amount
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     */
    public function refund(Orders $order, $amount)
    {
        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException('DIBS api refund action: order contains no transaction id, order id was: '.$order->getId());
        }

        $transaction      = $attributes->payment->transact;
        $paymentGatewayId = $order->getPaymentGatewayId();
        $currency         = $this->api->currencyCodeToNum($order->getCurrencyCode());
        $amount           = $this->api->formatAmount($amount);
        $stringToHash     = 'merchant=' . $this->settings['merchant'] . '&orderid=' . $paymentGatewayId . '&transact=' . $transaction . '&amount=' . $amount;

        $params = [
            'merchant'  => $this->settings['merchant'],
            'amount'    => $amount,
            'currency'  => $currency,
            'transact'  => $transaction,
            'textreply' => 'true',
            'md5key'    => $this->api->md5keyFromString($stringToHash),
            'orderid'   => $paymentGatewayId,
        ];

        return $this->call('cgi-adm/refund.cgi', $params, self::USE_AUTH_HEADERS);
    }

    /**
     * payinfo
     *
     * Called getStatusByTransid or getStatus in old system
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     **/
    public function payinfo( Orders $order )
    {
        $attributes  = $order->getAttributes();
        $transaction = $attributes->payment->transact;

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException('DIBS api payinfo action: order contains no transaction id, order id was: '.$order->getId());
        }

        return $this->call( 'cgi-adm/payinfo.cgi', ['transact' => $transaction], self::USE_AUTH_HEADERS);
    }

    /**
     * transstatus
     * http://tech.dibs.dk/dibs-api/status-functions/transstatuspml/
     *
     * Called getTransStatus or getTransStatusByTransId in old system
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     **/
    public function transstatus(Orders $order)
    {
        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException('DIBS api transstatus action: order contains no transaction id, order id was: '.$order->getId());
        }

        $transaction = $attributes->payment->transact;

        $params = [
            'merchant' => $this->settings['merchant'],
            'transact' => $transaction,
        ];

        return $this->call('transstatus.pml', $params);
    }

    /**
     * http://tech.dibs.dk/dibs_api/status_functions/callbackcgi/
     *
     * Called getTransactionDataByTransactionId in old system
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     **/
    public function callback(Orders $order)
    {
        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException('DIBS api callback action: order contains no transaction id, order id was: '.$order->getId());
        }

        $params = [
            'merchant' => $this->settings['merchant'],
            'transact' => $attributes->payment->transact,
        ];

        return $this->call('cgi-adm/callback.cgi', $params, self::USE_AUTH_HEADERS);
    }

    /**
     * http://tech.dibs.dk/dibs_api/status_functions/transinfocgi/
     *
     * Called getTransInfo in old system
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     *
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function transinfo(Orders $order)
    {
        $paymentGatewayId = $order->getPaymentGatewayId();
        $currency         = $this->api->currencyCodeToNum($order->getCurrencyCode());
        $amount           = $this->api->formatAmount($order->getTotalPrice());

        $params = [
            'merchant'  => $this->settings['merchant'],
            'amount'    => $amount,
            'orderid'   => $paymentGatewayId,
            'currency'  => $currency,
        ];

        return $this->call('cgi-bin/transinfo.cgi', $params);
    }
}
