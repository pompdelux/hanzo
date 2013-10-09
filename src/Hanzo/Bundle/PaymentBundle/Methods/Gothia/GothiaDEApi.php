<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Gothia;

use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApi;

use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Request;

class GothiaDEApi extends GothiaApi
{

    public function __construct($params, Array $settings)
    {
        parent::__construct($params, $settings);

        if (isset($settings['paytypes'])) {
            $this->settings['paytypes'] = explode(',', $settings['paytypes']);
        } else {
            $this->settings['paytypes'] = array('gothia');
        }
    }


    /**
     * getPaytypes
     * @return void
     **/
    public function getPayTypes()
    {
        return $this->settings['paytypes'];
    }

    /**
     * Overridden updateOrderSuccess function.
     */
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->save();
    }

    /**
     * Overridden updateOrderFailed function.
     */
    public function updateOrderFailed( Request $request, Orders $order)
    {
        $order->setState( Orders::STATE_ERROR_PAYMENT );
        $order->save();
    }

    /**
     * Overridden getProcessButton function.
     */
    public function getProcessButton(Orders $order, Request $request)
    {
        return ['url' => 'payment/gothia-de'];
    }
}
