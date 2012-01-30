<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle;

use Hanzo\Bundle\ShippingBundle\ShippingMethods\ShippingMethod
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
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct()
    {
    }

    /**
     * getMethods
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getMethods()
    {
        $methods = array(
            // DK:
            '10' => new ShippingMethod( 'Post Danmark', 'Privat', 'Fragt til private', '10', 'flat' ),
            '11' => new ShippingMethod( 'Post Danmark', 'Erhverv', 'Fragt til erhverv', '11', 'flat' ),
            '12' => new ShippingMethod( 'Post Danmark', 'Døgnpost', '', '12', 'flat' ),
            // COM:
            '20' => new ShippingMethod( 'Post Danmark', 'Overseas', '', '20', 'flat' ),
            // SE:
            '30' => new ShippingMethod( 'Privat bulksplit', 'Privat bulksplit', '', '30', 'flat' ),
            // NO:
            'P' => new ShippingMethod( 'Post Danmark', 'På døren', '', 'P', 'flat' ),
            'S' => new ShippingMethod( 'Post Danmark', 'Servicepakke', '', 'S', 'flat' ),
            // NL:
            '60' => new ShippingMethod( 'DPD', 'DPD', '', '60', 'flat' ),
            );
        return $methods;
    }
} // END class ShippingApi
