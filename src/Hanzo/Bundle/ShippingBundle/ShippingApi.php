<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Countries;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\FreeShippingQuery;
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
        if (!isset($settings['methods_enabled'])) {
          return false;
        }

        $methodsEnabled = unserialize( $settings['methods_enabled'] );
        $hanzo = Hanzo::getInstance();
        $domain_key = $hanzo->get('core.domain_key');

        $query = ShippingMethodsQuery::create()
            ->filterByIsActive(1)
            ->filterById($methodsEnabled)
            ->find()
        ;

        $order    = OrdersPeer::getCurrent();
        $break_at = $order->getCreatedAt('Y-m-d');

        // shipping fee check
        $free_limit = FreeShippingQuery::create('fs')
            ->select('break_at')
            ->filterByDomainKey($domain_key)
            ->condition('condition_1', 'fs.ValidFrom <= ?', $break_at)
            ->condition('condition_2', 'fs.ValidTo >= ?', $break_at)
            ->combine(array('condition_1', 'condition_2'), 'and', 'condition_12')
            ->condition('condition_3', 'fs.ValidFrom IS NULL')
            ->condition('condition_4', 'fs.ValidTo IS NULL')
            ->combine(array('condition_3', 'condition_4'), 'and', 'condition_34')
            ->where(array('condition_12', 'condition_34'), 'or')
            ->orderByValidFrom('DESC')
            ->findOne()
        ;

        if ($free_limit > 0) {
            $total = $order->getTotalPrice(true);
        }

        foreach ($query as $q)
        {
            if ($free_limit && ($total > $free_limit)) {
                $q->setPrice(0.00);
            } elseif ('COM' == $domain_key) {

                // TODO: do not hardcode !
                $c = $order->getCountriesRelatedByDeliveryCountriesId();
                if (!$c instanceof Countries) {
                    $c = $order->getCountriesRelatedByBillingCountriesId();
                }

                if ($c && ('EU' != $c->getContinent())) {
                    switch ($q->getExternalId()) {
                        case '20':
                            $q->setPrice(20.00);
                        break;
                    }
                }
            }

            $this->methods[ $q->getExternalId() ] = $q;
        }

        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');
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

/**
 * Switch from external_id to id to facility multiple prices:
 * dk:
 * UPDATE `domains_settings` SET c_value = 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'DK'
 *
 * salesdk:
 * UPDATE `domains_settings` SET c_value = 'a:3:{i:0;i:6;i:1;i:7;i:2;i:8;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'SalesDK'
 *
 * com:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:4;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'COM'
 *
 * se:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:1;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'SE'
 *
 * salesse:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:2;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'SalesSE'
 *
 * no:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:1;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'NO'
 *
 * salesno:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:2;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'SalesNO'
 *
 * nl:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:5;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'NL'
 *
 * salesnl:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:6;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'SalesNL'
 *
 * fi:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:1;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'FI'
 *
 * salesfi:
 * UPDATE `domains_settings` SET c_value = 'a:1:{i:0;i:2;}' WHERE ns = 'shippingapi' AND c_key = 'methods_enabled' AND domain_key = 'SalesFI'
 *
 */
