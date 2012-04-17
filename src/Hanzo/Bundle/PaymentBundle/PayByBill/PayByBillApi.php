<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\PayByBill;

use Exception;

use Hanzo\Model\Orders;

use Symfony\Component\HttpFoundation\Request;

class PayByBillApi
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
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function isActive()
    {
        return ( isset($this->settings['active']) ) ? $this->settings['active'] : false;
    }


    /**
     * updateOrderSuccess
     *
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->setAttribute( 'paytype' , 'payment', 'paybybill' );
        $order->save();
    }
}
