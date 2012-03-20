<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\PayByBill;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Hanzo\Model\Orders;

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
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
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
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function isActive()
    {
        return ( isset($this->settings['active']) ) ? $this->settings['active'] : false;
    }


    /**
     * updateOrderSuccess
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->setAttribute( 'paytype' , 'payment:gateway', 'paybybill' );
        $order->save();
    }
}
