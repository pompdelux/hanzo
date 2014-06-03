<?php

namespace Hanzo\Bundle\DiscountBundle\Controller;

use Hanzo\Model\ProductsQuantityDiscountQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\CoreController;

class QuantityDiscountController extends CoreController
{
    /**
     * @Template()
     * @param  string $master
     * @return array
     */
    public function discountTableAction($master)
    {
        $time  = time();
        $table = ProductsQuantityDiscountQuery::create()
            ->filterByProductsMaster($master)
            ->filterByValidFrom($time, \Criteria::LESS_EQUAL)
            ->_or()
            ->filterByValidFrom(null, \Criteria::ISNULL)
            ->filterByValidTo($time, \Criteria::GREATER_EQUAL)
            ->_or()
            ->filterByValidTo(null, \Criteria::ISNULL)
            ->useDomainsQuery()
                ->filterByDomainKey($this->container->get('kernel')->getSetting('domain_key'))
            ->endUse()
            ->find()
        ;

        return ['table' => $table];
    }
}
