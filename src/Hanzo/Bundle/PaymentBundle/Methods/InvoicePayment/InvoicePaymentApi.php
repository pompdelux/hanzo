<?php /* vim: set sw=4: */

/**

To enable on SalesDK:

INSERT INTO domains_settings
    (domain_key, ns, c_key, c_value)
VALUES
    ('SalesDK', 'invoicepaymentapi', 'method_enabled', '1'),
    ('SalesDK', 'invoicepaymentapi', 'order', '70');

*/

namespace Hanzo\Bundle\PaymentBundle\Methods\InvoicePayment;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\Customers;
use Hanzo\Bundle\PaymentBundle\BasePaymentApi;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;
use Symfony\Component\HttpFoundation\Request;

class InvoicePaymentApi extends BasePaymentApi implements PaymentMethodApiInterface
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
     * @return InvoicePaymentCallResponse
     **/
    public function cancel(Customers $customer, Orders $order)
    {
        return new InvoicePaymentCallResponse();
    }

    /**
     * isActive
     * Checks if the api is active for the current configuration
     *
     * @return bool
     **/
    public function isActive()
    {
        // always allow test and dev env to use ManualPayment
        if (preg_match('/^dev_|test_/', $this->environment)) {
            return true;
        }

        $order = OrdersPeer::getCurrent();
        if ($order->getInEdit()) {
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
     * @param Request $request
     * @param Orders  $order
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderFailed( Request $request, Orders $order)
    {
        $order->setState( Orders::STATE_ERROR_PAYMENT );
        $order->setAttribute( 'paytype' , 'payment', 'invoicepayment' );
        $order->save();
    }

    /**
     * updateOrderSuccess
     *
     * TODO: priority: low, should use shared methods between all payment methods
     *
     * @param Request $request
     * @param Orders  $order
     * @return void
     * @author Ulrik Nielsen <un@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->setAttribute( 'paytype' , 'payment', 'invoicepayment' );
        $order->save();
    }

    /**
     * @param Orders  $order
     * @param Request $request
     *
     * @return array
     */
    public function getProcessButton(Orders $order, Request $request)
    {
        return ['url' => 'payment/invoicepayment/callback'];
    }
}
