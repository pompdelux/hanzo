<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle;

use Hanzo\Core\Hanzo,
    Hanzo\Model\ShippingMethods,
    Hanzo\Model\ShippingMethodsPeer,
    Hanzo\Model\ShippingMethodsQuery
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
     * undocumented class variable
     *
     * @var string
     **/
    protected $methods = array();

    /**
     * __construct
     * @param array $params
     * @param array $settings
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $params, $settings )
    {
        // TODO: handle free shipping
        error_log(__LINE__.':'.__FILE__.' '.print_r($settings,1)); // hf@bellcom.dk debugging

        if ( !isset( $settings['methods_enabled'] ) )
        {
          return false;
        }

        $methodsEnabled = unserialize( $settings['methods_enabled'] );

        $query = ShippingMethodsQuery::create()
            ->filterByIsActive(1)
            ->filterByExternalId($methodsEnabled)
            ->find();

        foreach ($query as $q) 
        {
            $this->methods[ $q->getExternalId() ] = $q;
        }

        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');;
    }

    /**
     * isMethodAvaliable
     * @param int $axId The id of the shipping method in AX
     * @return bool
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function isMethodAvaliable( $axId )
    {
        $methods = $this->getMethods();
        return isset($methods[$axId]);
    }

    /**
     * getMethods
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getMethods()
    {
        /*switch ($this->domainKey) 
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
        }*/

        return $this->methods;
    }
} // END class ShippingApi
