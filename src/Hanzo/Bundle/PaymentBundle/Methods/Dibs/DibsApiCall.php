<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Dibs;

use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

use Hanzo\Core\Hanzo;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiCallInterface;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApi;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApiCallResponse;

class DibsApiCall implements PaymentMethodApiCallInterface
{
    /**
     * undocumented class variable
     *
     * @var bool
     **/
    const USE_AUTH_HEADERS = true;

    /**
     * undocumented class variable
     *
     * @var DibsApiCall instance
     **/
    private static $instance = null;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $baseUrl = 'https://payment.architrade.com/';

    /**
     * undocumented class variable
     *
     * @var array
     **/
    protected $settings = array();

    /**
     * undocumented class variable
     *
     * @var DibsApi
     **/
    protected $api = null;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function __construct() {}

    /**
     * someFunc
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public static function getInstance( Array $settings, $api )
    {
        if ( self::$instance === null )
        {
            self::$instance = new self;
        }

        self::$instance->settings = $settings;
        self::$instance->api = $api;

        return self::$instance;
    }

    /**
     * Curl wrapper
     * @param string $function Only the last part of the url, e.g. 'cgi-bin/dostuff.cgi'
     * @param array $params The data that is send to dibs
     * @param bool $useAuthHeaders Should extran authorization headers be send in request
     * @return DibsApiCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function call( $function, array $params, $useAuthHeaders = false )
    {
        //$logger = Hanzo::getInstance()->container->get('logger');
        $ch = curl_init();

        $url = $this->baseUrl . $function;

        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $headers = array();

        if ($useAuthHeaders) {
            if (!isset($this->settings['api_user']) || !isset($this->settings['api_pass'])) {
                throw new DibsApiCallException( 'DIBS api: Missing api username or/and password' );
            }

            $headers = array( 'Authorization: Basic '. base64_encode($this->settings['api_user'].':'.$this->settings['api_pass']) );

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
        }

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response === false) {
            throw new DibsApiCallException('Kommunikation med DIBS fejlede, fejlen var: "'.curl_error($ch).'"');
        }

        return new DibsApiCallResponse( $response, $function );
    }

    /**
     * Query dibs for status om acquirers
     *
     * @param string $acquirer
     * @return DibsApiCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function status( $acquirer = 'all' )
    {
        $params = array(
            'replytype' => 'html',
            'acquirer'  => $acquirer,
        );

        return $this->call( 'status.pml', $params );
    }

    /**
     * Cancel payment
     * http://tech.dibs.dk/dibs-api/payment-functions/cancelcgi/
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancel( Customers $customer, Orders $order )
    {
        $attributes = $order->getAttributes();

        if ( !isset($attributes->payment->transact) )
        {
            throw new DibsApiCallException( 'DIBS api cancel action: order contains no transaction id, order id was: '.$order->getId() );
        }

        $transaction = $attributes->payment->transact;
        $paymentGatewayId = $order->getPaymentGatewayId();

        $stringToHash = 'merchant='. $this->settings[ 'merchant' ] .'&orderid='. $paymentGatewayId.'&transact='.$transaction;

        $params = array(
            'merchant'  => $this->settings[ 'merchant' ],
            'transact'  => $transaction,
            'textreply' => 'true',
            'md5key'    => $this->api->md5keyFromString( $stringToHash ),
            'orderid'   => $paymentGatewayId,
        );

        return $this->call('cgi-adm/cancel.cgi', $params, self::USE_AUTH_HEADERS );
    }

    /**
     * Capture transaction
     * http://tech.dibs.dk/dibs-api/payment-functions/capturecgi/
     *
     * @param Orders $order
     * @param int $amount Should be in smallest format, e.g. if you want to capture 175,50 the number should be 17550 (remember the last zero)
     * @return DibsApiCallResponse
     */
    public function capture( Orders $order, $amount )
    {
        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException( 'DIBS api capture action: order contains no transaction id, order id was: '.$order->getId() );
        }

        $transaction      = $attributes->payment->transact;
        $paymentGatewayId = $order->getPaymentGatewayId();
        $amount           = $this->api->formatAmount($amount);
        $stringToHash     = 'merchant='. $this->settings[ 'merchant' ] .'&orderid='. $paymentGatewayId.'&transact='.$transaction.'&amount='.$amount;

        $params = array(
            'amount'    => $amount,
            'merchant'  => $this->settings[ 'merchant' ],
            'transact'  => $transaction,
            'orderid'   => $paymentGatewayId,
            'textreply' => 'true',
            'md5key'    => $this->api->md5keyFromString( $stringToHash ),
            // 'force'     => 'true',
            // 'account'   => '',
        );

        return $this->call('cgi-bin/capture.cgi', $params );
    }

    /**
     * Cancels a captured transaction and transfers the money back to the card holders account
     * http://tech.dibs.dk/dibs-api/payment-functions/refundcgi/
     *
     * @param Orders $order
     * @param int $amount
     * @return DibsApiCallResponse
     */
    public function refund( Orders $order, $amount )
    {
        $attributes = $order->getAttributes();

        if ( !isset($attributes->payment->transact) )
        {
            throw new DibsApiCallException( 'DIBS api refund action: order contains no transaction id, order id was: '.$order->getId() );
        }

        $transaction      = $attributes->payment->transact;
        $paymentGatewayId = $order->getPaymentGatewayId();
        $currency         = $this->api->currencyCodeToNum($order->getCurrencyCode());
        $amount           = $this->api->formatAmount($amount);

        $stringToHash = 'merchant='. $this->settings[ 'merchant' ] .'&orderid='. $paymentGatewayId.'&transact='.$transaction.'&amount='.$amount;

        $params = array(
            'merchant'  => $this->settings[ 'merchant' ],
            'amount'    => $amount,
            'currency'  => $currency,
            'transact'  => $transaction,
            'textreply' => 'true',
            'md5key'    => $this->api->md5keyFromString( $stringToHash ),
            'orderid'   => $paymentGatewayId,
        );

        return $this->call('cgi-adm/refund.cgi', $params, self::USE_AUTH_HEADERS );
    }

    /**
     * payinfo
     *
     * Called getStatusByTransid or getStatus in old system
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function payinfo( Orders $order )
    {
        $attributes       = $order->getAttributes();
        $transaction      = $attributes->payment->transact;

        if ( !isset($attributes->payment->transact) )
        {
            throw new DibsApiCallException( 'DIBS api payinfo action: order contains no transaction id, order id was: '.$order->getId() );
        }

        $params = array(
            'transact'  => $transaction,
        );

        return $this->call( 'cgi-adm/payinfo.cgi', $params, self::USE_AUTH_HEADERS );
    }

    /**
     * transstatus
     * http://tech.dibs.dk/dibs-api/status-functions/transstatuspml/
     *
     * Called getTransStatus or getTransStatusByTransId in old system
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function transstatus( Orders $order )
    {
        $attributes       = $order->getAttributes();

        if ( !isset($attributes->payment->transact) )
        {
            throw new DibsApiCallException( 'DIBS api transstatus action: order contains no transaction id, order id was: '.$order->getId() );
        }

        $transaction      = $attributes->payment->transact;

        $params = array(
            'merchant'  => $this->settings[ 'merchant' ],
            'transact' => $transaction,
        );

        // not used here
        $statusCodes = array(
            0 => 'transaction inserted (not approved)',
            1 => 'declined',
            2 => 'authorization approved',
            3 => 'capture sent to acquirer',
            4 => 'capture declined by acquirer',
            5 => 'capture completed',
            6 => 'authorization deleted',
            7 => 'capture balanced',
            8 => 'partially refunded and balanced',
            9 => 'refund sent to acquirer',
            10 => 'refund declined',
            11 => 'refund completed',
            13 => '"ticket" transaction',
            14 => 'deleted "ticket" transaction',
        );

        return $this->call( 'transstatus.pml', $params);
    }

    /**
     * http://tech.dibs.dk/dibs_api/status_functions/callbackcgi/
     *
     * Called getTransactionDataByTransactionId in old system
     *
     * @param Orders $order
     * @return DibsApiCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function callback( Orders $order )
    {
        $attributes = $order->getAttributes();

        if (!isset($attributes->payment->transact)) {
            throw new DibsApiCallException( 'DIBS api callback action: order contains no transaction id, order id was: '.$order->getId() );
        }

        $params = array(
            'merchant' => $this->settings[ 'merchant' ],
            'transact' => $attributes->payment->transact,
        );

        return $this->call( 'cgi-adm/callback.cgi', $params, self::USE_AUTH_HEADERS );
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
    public function transinfo( Orders $order )
    {
        $paymentGatewayId = $order->getPaymentGatewayId();
        $currency         = $this->api->currencyCodeToNum($order->getCurrencyCode());
        $amount           = $this->api->formatAmount($order->getTotalPrice());

        $params = array(
            'merchant'  => $this->settings[ 'merchant' ],
            'amount'    => $amount,
            'orderid'   => $paymentGatewayId,
            'currency'  => $currency,
        );

        return $this->call( 'cgi-bin/transinfo.cgi', $params);
    }
}
