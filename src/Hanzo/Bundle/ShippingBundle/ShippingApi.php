<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle;

use Hanzo\Core\Hanzo,
    Hanzo\Bundle\ShippingBundle\ShippingMethods\ShippingMethod
    ;

/**
 * undocumented class
 *
 * @packaged default
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class ShippingApi
{
    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $domainKey;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct()
    {
        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');;
    }

    /**
     * getMethods
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getMethods()
    {
        $methods = array();

        switch ($this->domainKey) 
        {
            case 'DK':
                $methods = array(
                    '10' => new ShippingMethod( 'Post Danmark', 'Privat', 'Fragt til private', '10', 'flat' ),
                    '11' => new ShippingMethod( 'Post Danmark', 'Erhverv', 'Fragt til erhverv', '11', 'flat' ),
                    '12' => new ShippingMethod( 'Post Danmark', 'Døgnpost', '', '12', 'flat' ),
                );
                break;
            case 'COM':
                $methods = array(
                    '20' => new ShippingMethod( 'Post Danmark', 'Overseas', '', '20', 'flat' ),
                );
                break;
            case 'SE':
                $methods = array(
                    '30' => new ShippingMethod( 'Privat bulksplit', 'Privat bulksplit', '', '30', 'flat' ),
                );
                break;
            case 'NO':
                $methods = array(
                    'P' => new ShippingMethod( 'Post Danmark', 'På døren', '', 'P', 'flat' ),
                    'S' => new ShippingMethod( 'Post Danmark', 'Servicepakke', '', 'S', 'flat' ),
                );
                break;
            case 'NL':
                $methods = array(
                    '60' => new ShippingMethod( 'DPD', 'DPD', '', '60', 'flat' ),
                );
                break;
        }

        return $methods;
    }
} // END class ShippingApi
