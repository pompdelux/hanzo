<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Gothia;

use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Request;
use Hanzo\Bundle\PaymentBundle\BasePaymentApi;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;

use Exception;

class GothiaApi extends BasePaymentApi implements PaymentMethodApiInterface
{
    /**
     * undocumented class variable
     *
     * @var string
     **/
    public $mode;

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
    public function __construct($params, Array $settings)
    {
        $this->settings = $settings;

        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);

        if ($this->settings['active'] === true) {
            $this->checkSettings($settings);
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
            'test',
            'username',
            'password',
            'clientId',
        );

        $missing = array();
        foreach ($requiredFields as $field) {
            if (!isset($settings[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new Exception( 'GothiaApi: missing settings: '. implode(',',$missing) );
        }
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
        return (isset($this->settings['active'])) ? $this->settings['active'] : false;
    }

    /**
     * getTest
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getTest()
    {
        return (isset($this->settings['test']) && strtoupper($this->settings['test']) == 'YES') ? true : false;
    }

    /**
     * getFeeExternalId
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFeeExternalId()
    {
        return (isset($this->settings['fee.id'])) ? $this->settings['fee.id'] : null;
    }

    /**
     * someFunc
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function call()
    {
        return GothiaApiCall::getInstance($this->settings, $this);
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
        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->setAttribute( 'paytype' , 'payment', 'gothia' );
        // Fee is handled in the checkout controller, as we need the information in the summery
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
        $order->setState( Orders::STATE_ERROR_PAYMENT );
        $order->setAttribute( 'paytype' , 'payment', 'gothia' );
        $order->save();
    }


    public function getProcessButton(Orders $order, Request $request)
    {
        return ['url' => 'payment/gothia'];
    }
}
