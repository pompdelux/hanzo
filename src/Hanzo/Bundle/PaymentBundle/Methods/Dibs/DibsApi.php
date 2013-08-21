<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Dibs;

use Exception;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Bundle\PaymentBundle\BasePaymentApi;
use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApiCall;
use Hanzo\Bundle\PaymentBundle\Methods\Dibs\DibsApiCallException;

use Symfony\Component\HttpFoundation\Request;

class DibsApi extends BasePaymentApi implements PaymentMethodApiInterface
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

    /**
     * __construct
     * @return void
     */
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
