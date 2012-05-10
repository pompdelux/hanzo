<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Gothia;

use Hanzo\Bundle\PaymentBundle\PaymentMethodApiInterface;

class GothiaApi implements PaymentMethodApiInterface
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
    public function __construct($params, $settings)
    {
        // FIXME: missing
        // - set active
        // TODO: check for missing settings
        $this->settings = $settings;

        // FIXME: hardcoded vars:
        $this->settings['test'] = true;

        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);

        // Live settings
        /*$this->settings = array(
            'username' => 'PompDeLuxExternalSE',
            'password' => 'i4F1FfFJ',
            'clientID' => 7757,
        );*/

        // Test settings:
        /*$this->settings = array(
            'username' => 'EXTPompDeLuxSETest',
            'password' => 'o6K7IGPR',
            'clientID' => 7012,
        );*/
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
        return ( isset($this->settings['active']) ) ? $this->settings['active'] : false;
    }

    /**
     * someFunc
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function call()
    {
        return GothiaApiCall::getInstance($this->settings);
    }
}
