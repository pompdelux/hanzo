<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ShippingMethods;
use Hanzo\Model\ShippingMethodsPeer;
use Hanzo\Model\ShippingMethodsQuery;

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
     *
     * @param array $params
     * @param array $settings
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $params, $settings )
    {
        if ( !isset( $settings['methods_enabled'] ) )
        {
          return false;
        }

        $methodsEnabled = unserialize( $settings['methods_enabled'] );

        $query = ShippingMethodsQuery::create()
            ->filterByIsActive(1)
            ->filterByExternalId($methodsEnabled)
            ->find();

        // shipping fee check
        $fee_limit = Hanzo::getInstance()->get('shipping.free_shipping', 10);
        if ($fee_limit > 0) {
            $total = OrdersPeer::getCurrent()->getTotalPrice(true);
        }

        foreach ($query as $q)
        {
            if ($fee_limit && ($total > $fee_limit)) {
                $q->setPrice(0.00);
            }
            $this->methods[ $q->getExternalId() ] = $q;
        }

        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');;
    }

    /**
     * isMethodAvaliable
     *
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
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getMethods()
    {
        return $this->methods;
    }
} // END class ShippingApi
