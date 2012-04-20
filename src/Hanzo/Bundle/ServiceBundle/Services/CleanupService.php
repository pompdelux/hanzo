<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Criteria;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;

class CleanupService
{
    protected $parameters;
    protected $settings;

    public function __construct($parameters, $settings)
    {
        $this->parameters = $parameters;
        $this->settings = $settings;
    }

    public function deadOrders()
    {
        $orders = OrdersQuery::create()
            ->filterByState(0, Criteria::LESS_THAN)
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('3 hours ago')), Criteria::LESS_THAN)
            ->find();

        Tools::log(get_class_methods($orders));

    }
}
