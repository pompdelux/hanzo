<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Dibs;

use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DibsApi
 * @package Hanzo\Bundle\PaymentBundle\Methods\Dibs
 *
 * @method array       buildFormFields(Orders $order)
 * @method DibsApiCall call()
 * @method array       checkSettings(array $settings)
 * @method bool        isActive()
 * @method void        updateOrderFailed(Request $request, Orders $order)
 * @method void        updateOrderSuccess(Request $request, Orders $order)
 * @method void        verifyCallback(Request $callbackRequest, Orders $order)
 */
class DibsApi
{
    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @var Router
     */
    protected $router;

    /**
     * Holds the active service type object
     * @var object
     */
    protected $service;

    public function __construct( $parameters, array $settings )
    {
        $this->router = $parameters[0];

        $this->settings = $settings;
        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);

        if (isset($settings['paytypes'])) {
            $this->settings['paytypes'] = unserialize($settings['paytypes']);
        }


        // FlexWin as default - catches "old" gateways. DibsPaymentWindow is the new "black"
        $settings['type'] = isset($settings['type']) ?
            $settings['type'] :
            'FlexWin'
        ;

        $class = __NAMESPACE__.'\\Type\\'.$settings['type'];

        $this->service = new $class($parameters, $settings);

        if ($this->settings['active'] === true) {
            $this->checkSettings($settings);
        }
    }

    /**
     * wraps all calls so we can initiate the right service type
     *
     * @param  string $method    Method name
     * @param  array  $arguments Array of arguments to parse along
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        return call_user_func_array([$this->service, $method], $arguments);
    }
}
