<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Gothia;

use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Request;
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
    public function __construct($params, Array $settings)
    {
        $this->settings = $settings;

        $this->settings['active'] = (isset($this->settings['method_enabled']) && $this->settings['method_enabled'] ? true : false);

        if ( $this->settings['active'] === true)
        {
            $this->checkSettings($settings);
        }

        /*
        Live settings:
        'username' = 'PompDeLuxExternalSE'
        'password' = 'i4F1FfFJ'
        'clientId' = 7757

        Test settings:
        'username' = 'EXTPompDeLuxSETest'
        'password' = 'o6K7IGPR'
        'clientId' = 7012
        */
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

        foreach ($requiredFields as $field) 
        {
            if ( !isset($settings[$field]) )
            {
                $missing[] = $field;
            }
        }

        if ( !empty($missing) )
        {
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

    /**
     * updateOrderSuccess
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateOrderSuccess( Request $request, Orders $order )
    {
        $order->setState( Orders::STATE_PAYMENT_OK );
        $order->setAttribute( 'paytype' , 'payment', 'gothia' );
        $order->save();
    }
}
