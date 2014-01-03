<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\ManualPayment;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\Customers;

use Hanzo\Bundle\PaymentBundle\BasePaymentApi;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;


use Symfony\Component\HttpFoundation\Request;

class ManualPaymentApi extends BasePaymentApi implements PaymentMethodApiInterface
{
    /**
     * undocumented class variable
     *
     * @var array
     **/
    protected $settings = array();

    /**
     * @var string
     */
    protected $environment;

    /**
     * __construct
     *
     * @param $params
     * @param $settings
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function __construct( $params, $settings )
    {
        $this->environment        = $params[0];
        $this->settings           = $settings;
        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);
    }

    /**
     * call
     * Dummy implementation as this method does not use an api call
     * @return boolean
     **/
    public function call()
    {
        return $this;
    }

    /**
     * cancel
     *
     * @param $customer
     * @param $order
     * @return ManualPaymentCallResponse
     **/
    public function cancel(Customers $customer, Orders $order)
    {
        return new ManualPaymentCallResponse();
    }

    /**
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     **/
    public function isActive()
    {
        $order = OrdersPeer::getCurrent();
        if ($order->getInEdit() && (!preg_match('/^dev_/', $this->environment))) {
            return false;
        }
        return ( isset($this->settings['active']) ) ? $this->settings['active'] : false;
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
        $order->setAttribute( 'paytype' , 'payment', 'manualpayment' );
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
        $order->setAttribute( 'paytype' , 'payment', 'manualpayment' );
        $order->save();
    }


    public function getProcessButton(Orders $order, Request $request)
    {
        return ['url' => 'payment/manualpayment/callback'];
    }
}
