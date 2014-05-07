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
        $table = ProductsQuantityDiscountQuery::create()
            ->filterByProductsMaster($master)
            ->useDomainsQuery()
                ->filterByDomainKey($this->container->get('kernel')->getSetting('domain_key'))
            ->endUse()
            ->find()
        ;

        return ['table' => $table];
    }
}
