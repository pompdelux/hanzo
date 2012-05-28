<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Dibs;

use Exception;

use Hanzo\Core\Hanzo,
    Hanzo\Model\Orders,
    Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCall,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException
    ;

use Symfony\Component\HttpFoundation\Request;

class DibsApi implements PaymentMethodApiInterface
{
    /**
     * map currencies to dibs currency codes
     *
     * @var array
     */
    protected $currency_map = array(
        'DKK' => 208,
        'EUR' => 978,
        'USD' => 840,
        'GBP' => 826,
        'SEK' => 752,
        'AUD' => 036,
        'CAD' => 124,
        'ISK' => 352,
        'JPY' => 392,
        'NZD' => 554,
        'NOK' => 578,
        'CHF' => 756,
        'TRY' => 949,
    );

    /**
     * undocumented class variable
     *
     * @var array
     **/
    protected $settings = array();

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $params, Array $settings )
    {
        $this->settings = $settings;
        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);

        if ( $this->settings['active'] === true)
        {
            $this->checkSettings($settings);
        }

        if ( isset($settings['paytypes']) )
        {
            $this->settings['paytypes'] = unserialize($settings['paytypes']);
        }
    }

    /**
     * checkSettings
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function checkSettings(Array $settings)
    {
        $requiredFields = array(
            'method_enabled',
            'paytypes',
            'merchant',
            'test',
            'md5key1',
            'md5key2',
            );

        $missing = array();

        foreach ($requiredFields as $field)
        {
            if ( !isset($settings[$field]) )
            {
                $missing[] = $field;
            }
        }

        if ( !empty($missing) )
        {
            throw new Exception( 'DibsApi: missing settings: '. implode(',',$missing) );
        }
    }

    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * getEnabledPaytypes
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getEnabledPaytypes()
    {
        return $this->settings['paytypes'];
    }

    /**
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function isActive()
    {
        return ( isset($this->settings['active']) ) ? $this->settings['active'] : false;
    }

    /**
     * getFee
     * @return float
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFee()
    {
        return ( isset($this->settings['fee']) ) ? $this->settings['fee'] : 0.00;
    }

    /**
     * getFeeExternalId
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFeeExternalId()
    {
        return ( isset($this->settings['fee.id']) ) ? $this->settings['fee.id'] : null;
    }

    /**
     * getMerchant
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getMerchant()
    {
        return $this->settings['merchant'];
    }

    /**
     * getTest
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getTest()
    {
        return $this->settings['test'];
    }

    /**
     * verifyCallback
     * @param Request $callbackRequest
     * @param Orders $order The current order object
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function verifyCallback( Request $callbackRequest, Orders $order )
    {
        // The order must be in the pre payment state, if not it has not followed the correct flow
        if ( $order->getState() != Orders::STATE_PRE_PAYMENT )
        {
            throw new Exception( 'The order is not in the correct state "'. $order->getState() .'"' );
        }

        if ( $callbackRequest->get('merchant') != $this->settings['merchant'] )
        {
            throw new Exception( 'Wrong merchant "'. $callbackRequest->get('merchant') .'"' );
        }

        $currency = $this->currencyCodeToNum($order->getCurrencyCode());
        $amount   = self::formatAmount( $order->getTotalPrice() );

        // Should probably be handled by the payment method
        $gateway_id = $order->getPaymentGatewayId();
        $env = Hanzo::getInstance()->container->get('kernel')->getEnvironment();
        if ($env != 'prod' && strpos($gateway_id,$env.'_') === false ) {
            $gateway_id = $env . '_' . $gateway_id;
        }

        $calculated = $this->md5key( $gateway_id, $currency, $amount );

        if ( $callbackRequest->get('md5key') != $calculated )
        {
            error_log(__LINE__.':'.__FILE__.' '.print_r($_POST,1)); // hf@bellcom.dk debugging
            error_log(__LINE__.':'.__FILE__.' '.$gateway_id); // hf@bellcom.dk debugging
            error_log(__LINE__.':'.__FILE__.' '.$amount); // hf@bellcom.dk debugging
            throw new Exception( 'Md5 sum mismatch, got: "'. $callbackRequest->get('md5key') .'" expected: "'. $calculated .'"' );
        }

        $calculated = $this->md5AuthKey( $callbackRequest->get('transact'), $currency, $amount );

        if ( $callbackRequest->get('authkey') != $calculated )
        {
            throw new Exception( 'Authkey md5 sum mismatch, got: "'. $callbackRequest->get('authkey') .'" expected: "'. $calculated .'"' );
        }
    }

    /**
     * updateOrderSuccess
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $fields = array(
            'paytype',
            'cardnomask',
            'cardprefix',
            'acquirer',
            'cardexpdate',
            'currency',
            'ip',
            'approvalcode',
            'transact',
        );

        foreach ($fields as $field)
        {
            $order->setAttribute( $field , 'payment', $request->get($field) );
        }

        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->save();
    }

    /**
     * updateOrderFailed
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderFailed( Request $request, Orders $order)
    {
        $fields = array(
            'paytype',
            'cardnomask',
            'cardprefix',
            'acquirer',
            'cardexpdate',
            'currency',
            'ip',
            'approvalcode',
            'transact',
        );

        foreach ($fields as $field)
        {
            $order->setAttribute( $field , 'payment', $request->get($field) );
        }

        $order->setState( Orders::STATE_ERROR_PAYMENT );
        $order->save();
    }

    /**
     * call
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function call()
    {
        return DibsApiCall::getInstance($this->settings, $this);
    }

    /**
     * Calculate md5 sum for verification
     * @param int $orderId
     * @param int $currency
     * @param int $amount
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function md5key( $orderId, $currency, $amount )
    {
        return $this->md5keyFromString( 'merchant='. $this->settings['merchant'] .'&orderid='. $orderId .'&currency='.$currency.'&amount='.$amount);
    }

    /**
     * md5AuthKey
     * @param int $transact
     * @param int $currency
     * @param int $amount
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function md5AuthKey( $transact, $currency, $amount )
    {
        return $this->md5keyFromString( 'transact='.$transact.'&amount='.$amount.'&currency='.$currency );
    }

    /**
     * md5keyFromString
     * @param string $string containing the key=value pairs to be hashed
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function md5keyFromString( $string )
    {
        return md5( $this->settings['md5key2'] . md5( $this->settings['md5key1'] . $string));
    }

    /**
     * buildFormFields
     * @param Orders $order
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function buildFormFields( $gateway_id, $lang, Orders $order )
    {
        $orderId  = $gateway_id;
        $amount   = self::formatAmount( $order->getTotalPrice() );
        $currency = $this->currencyCodeToNum($order->getCurrencyCode());

        $settings = array(
            'orderid'      => $orderId,
            'amount'       => $amount,
            'lang'         => $lang,
            "merchant"     => $this->getMerchant(),
            "currency"     => $currency,
            // Set in the template:
            /*"cancelurl"    => "/payment/dibs/cancel",
            "callbackurl"  => "/payment/dibs/callback",
            "accepturl"    => "/payment/dibs/ok",*/
            "skiplastpage" => "YES",
            "uniqueoid"    => "YES",
            "test"         => $this->getTest(),
            "paytype"      => '', // This _must_ be set in the form
            "md5key"       => $this->md5key( $orderId, $currency, $amount ),
        );

        return $settings;
    }

    /**
     * formatAmount
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public static function formatAmount( $amount )
    {
        $amount = ( number_format( $amount, 2, '.', '') ) * 100 ;
        return $amount;
    }

    /**
     * currencyCodeToNum
     * Should at some point maybe use the currency_id set in the counties model
     * @return int
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function currencyCodeToNum( $code )
    {
        if ( isset($this->currency_map[$code]) )
        {
            return $this->currency_map[$code];
        }

        throw new Exception('DibsApi: unknown currency code: '.$code);
    }
}
