<?php

namespace Hanzo\Bundle\DiscountBundle\Event;

use Hanzo\Core\Hanzo;
use Hanzo\Model\Customers;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\ProductsQuantityDiscountQuery;
use Hanzo\Model\ProductsQuery;
use Symfony\Bridge\Monolog\Logger;
use Hanzo\Bundle\BasketBundle\Event\BasketEvent;

class BasketListener
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param BasketEvent $event
     */
    public function onBasketChange(BasketEvent $event)
    {
        $hanzo  = Hanzo::getInstance();
        $master = $event->getProduct()->getMaster();

        $breaks = ProductsQuantityDiscountQuery::create()
            ->orderBySpan(\Criteria::DESC)
            ->filterByDomainsId($hanzo->get('core.domain_id'))
            ->findByProductsMaster($master)
        ;

        if (0 === $breaks->count()) {
            return;
        }

        $order = $event->getOrder();

        // disable quantity discount for shopping advisors and employees - if there personal discounts is in effect.
        $customer = $order->getCustomers();
        if (($customer instanceof Customers) &&
            (1 < $customer->getGroupsId()) &&
            (0 == $hanzo->get('webshop.disable_discounts'))
        ) {
            return;
        }

        $ids = ProductsQuery::create()->select('Id')->findByMaster($master)->toArray();

        $c = new \Criteria();
        $c->add(OrdersLinesPeer::PRODUCTS_ID, $ids, \Criteria::IN);

        $total = 0;
        foreach ($order->getOrdersLiness($c) as $line) {
            $total += $line->getQuantity();
        }

        $discount = 0;
        foreach ($breaks as $break) {
            if ($total >= $break->getSpan()) {
                $discount = $break->getDiscount();
                break;
            }
        }

        foreach ($order->getOrdersLiness() as $line) {
            if (!in_array($line->getProductsId(), $ids)) {
                continue;
            }

            $line->setPrice($line->getOriginalPrice() - $discount);
            $line->save();
        }
    }
}
