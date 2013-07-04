<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Coupon;

use Exception;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\Customers;

use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;
use Hanzo\Bundle\PaymentBundle\Methods\Coupon\CouponCallResponse;

use Symfony\Component\HttpFoundation\Request;

class CouponApi implements PaymentMethodApiInterface
{
    /**
     * undocumented class variable
     *
     * @var array
     **/
    protected $settings = array();

    /**
     * __construct
     *
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function __construct( $params, $settings )
    {
        $this->settings = $settings;
        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);
    }

    /**
     * call
     * Dummy implementation as this method does not use an api call
     * @return boolean
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function call()
    {
        return $this;
    }

    /**
     * cancel
     *
     * @return CouponCallResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancel( Customers $customer, Orders $order )
    {
        return new CouponCallResponse();
    }

    /**
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function isActive()
    {
        $order = OrdersPeer::getCurrent();
        if ($order->getInEdit()) {
            return false;
        }

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
     * updateOrderFailed
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderFailed( Request $request, Orders $order)
    {
        $order->setState( Orders::STATE_ERROR_PAYMENT );
        $order->setAttribute( 'paytype' , 'payment', 'coupon' );
        $order->save();
    }

    /**
     * updateOrderSuccess
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->setAttribute( 'paytype' , 'payment', 'coupon' );
        $order->save();
    }


    public function getProcessButton(Orders $order, Request $request)
    {
        return ['url' => 'payment/coupon/callback'];
    }
}
