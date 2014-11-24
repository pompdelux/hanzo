<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Methods\Dibs;

use Hanzo\Model\Orders;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DibsApi
 *
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
     * Country code to currency map.
     * DIBS support the currencies listed here: http://tech.dibspayment.com/toolbox/currency_codes
     *
     * @var array
     */
    protected $currencyMap = [
        58  => 'DKK',
        161 => 'NOK',
        207 => 'SEK',
        208 => 'CHF',
    ];


    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var PaymentFixer
     */
    protected $fixer;

    /**
     * Holds the active service type object
     * @var object
     */
    protected $service;

    /**
     * @param array $parameters
     * @param array $settings
     */
    public function __construct($parameters, array $settings)
    {
        $this->router = $parameters[0];
        $this->logger = $parameters[1];
        $this->fixer  = $parameters[2];

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
     * @param string $method    Method name
     * @param array  $arguments Array of arguments to parse along
     *
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        return call_user_func_array([$this->service, $method], $arguments);
    }

    /**
     * Forwards the request to handleStaleEdits() these cleanup
     * tasks are the same for both stale and dead dibs orders
     *
     * @param Orders               $order
     * @param OutputInterface|null $outputInterface
     */
    public function handleAbandoned(Orders $order, $outputInterface = null)
    {
        $this->handleStaleEdits($order, $outputInterface);
    }

    /**
     * @param Orders               $order
     * @param OutputInterface|null $outputInterface
     *
     * @return mixed
     */
    public function handleStaleEdits(Orders $order, $outputInterface = null)
    {
        // only cleanup orders with state less than "payment ok"
        if ($order->getState() > Orders::STATE_PAYMENT_OK) {
            return;
        }

        $this->fixer->setOutputInterface($outputInterface);
        $this->fixer->resolve($order);
    }
}
