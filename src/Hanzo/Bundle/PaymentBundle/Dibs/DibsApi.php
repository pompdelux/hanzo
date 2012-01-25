<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Dibs;

use Exception;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Model\Orders,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCall,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException;

class DibsApi
{
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
    public function __construct( $params, $settings )
    {
        // FIXME: 
        // - define paytypes avaliable for domain
        // - set active
        // TODO: check for missing settings
        $this->settings = $settings;

        // FIXME: hardcode vars:
        $this->settings['active'] = true;
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

        $currency = $order->getCurrencyId();
        $amount   = self::formatAmount( $order->getTotalPrice() );

        $calculated = $this->md5( $order->getId(), $currency, $amount );

        if ( $callbackRequest->get('md5key') != $calculated )
        {
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
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );

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
            $order->setAttribute( $field , 'payment:gateway', $request->get($field) );
        }

        $order->save();
    }

    /**
     * updateOrdersFailed
     * @todo Should we save the same attributes as in updateOrderSuccess?
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrdersFailed( Request $request, Orders $order)
    {
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
        return DibsApiCall::getInstance($this->settings);
    }

    /**
     * Calculate md5 sum for verification
     * @return string 
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function md5( $orderId, $currency, $amount )
    {
        return md5( $this->settings['md5key2'] . md5( $this->settings['md5key1'] .'merchant='. $this->settings['merchant'] .'&orderid='. $orderId .'&currency='.$currency.'&amount='.$amount));
    }

    /**
     * md5AuthKey
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function md5AuthKey( $transact, $currency, $amount )
    {
        return md5( $this->settings['md5key2'] . md5( $this->settings['md5key1'] .'transact='.$transact.'&amount='.$amount.'&currency='.$currency));
    }

    /**
     * buildFormFields
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function buildFormFields( Orders $order )
    {
        // FIXME: hardcoded vars:
        $orderId  = 'test_'.date('His');
        $amount   = self::formatAmount( $order->getTotalPrice() );
        //$currency = $order->getCurrencyId();
        $currency = 208;
        $lang     = 'da';
        $payType  = 'DK';

        $settings = array(
            'orderid'      => $orderId,
            'amount'       => $amount,
            'lang'         => $lang, 
            "merchant"     => $this->getMerchant(),
            "currency"     => $currency,
            "cancelurl"    => "/payment/dibs/cancel",
            "callbackurl"  => "/payment/dibs/callback",
            "accepturl"    => "/payment/dibs/ok",
            "skiplastpage" => "YES",
            "uniqueoid"    => "YES",
            "test"         => $this->getTest(),
            "paytype"      => $payType,
            "md5key"       => $this->md5( $orderId, $currency, $amount ),
            );

        return $settings;
    }

    /**
     * formatAmount
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected static function formatAmount( $amount )
    {
        $amount = ( number_format( $amount, 2, '.', '') ) * 100 ; 
        return $amount;
    }
}
