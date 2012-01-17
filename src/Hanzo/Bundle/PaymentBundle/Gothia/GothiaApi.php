<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Gothia;

use Exception;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Model\Orders,
    Hanzo\Bundle\PaymentBundle\Gothia\GothiaApiCall,
    Hanzo\Bundle\PaymentBundle\Gothia\GothiaApiCallException;

class GothiaApi
{
    const MODE_TEST = 'test';
    const MODE_LIVE = 'live';

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
    public function __construct()
    {
        // FIXME: hardcoded settings
        /*$this->settings = array(
            'username' => 'PompDeLuxExternalSE',
            'password' => 'i4F1FfFJ',
            'clientID' => 7757,
        );*/

        $this->settings = array(
            'username' => 'EXTPompDeLuxSETest',
            'password' => 'o6K7IGPR',
            'clientID' => 7012,
        );

        $this->mode = self::MODE_TEST;
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
