<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\DiscountBundle\Handlers;

use Hanzo\Core\Hanzo;
use Hanzo\Model\Consultants;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Orders;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Psr\Log\LoggerInterface;

/**
 * Class PersonalDiscountHandler
 *
 * @package Hanzo\Bundle\DiscountBundle\Handlers
 */
class PersonalDiscountHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Orders
     */
    private $order;


    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param Orders $order
     *
     * @return self
     */
    public function initialize(Orders $order)
    {
        $this->order = $order;

        return $this;
    }


    /**
     * @return Orders
     */
    public function handle()
    {
        $discount = 0;
        $customer = $this->order->getCustomers();

        // prevent employees from getting stacked discounts
        if ($customer->getConsultants() instanceof Consultants) {
            $attributes = $this->order->getAttributes();

            if (isset($attributes->purchase->type) &&
                ('gift' === $attributes->purchase->type) &&
                ($this->order->getCustomersId() == CustomersPeer::getCurrent()->getId())
            ) {
                return $this->order;
            }
        }

        // apply group and private discounts if discounts is not disabled
        if (0 == Hanzo::getInstance()->get('webshop.disable_discounts')) {
            if ($customer->getDiscount()) {
                $discountLabel = 'discount.private';
                $discount      = $customer->getDiscount();
            } else {
                if ($customer->getGroups()) {
                    $discountLabel = 'discount.group';
                    $discount      = $customer->getGroups()->getDiscount();
                }
            }
        }

        // we do not stack discounts, so we need to recalculate the order lines
        if ($discount <> 0.00) {
            $lines = $this->order->getOrdersLiness();

            $productIds = [];
            foreach ($lines as $line) {
                if ('product' == $line->getType()) {
                    $productIds[] = $line->getProductsId();
                }
            }
            $prices = ProductsDomainsPricesPeer::getProductsPrices($productIds);

            $total = 0;
            foreach ($lines as $line) {
                if ('product' == $line->getType()) {
                    $price = $prices[$line->getProductsId()];

                    $line->setPrice($price['normal']['price']);
                    $line->setVat($price['normal']['vat']);
                    $line->setOriginalPrice($price['normal']['price']);

                    // only discountable products should be used.
                    if (true === $line->getProducts()->getIsDiscountable()) {
                        $total += ($line->getPrice() * $line->getQuantity());
                    }
                }
            }

            // so far _all_ discounts are handled as % discounts
            $discountAmount = ($total / 100) * $discount;
            $this->order->setDiscountLine($discountLabel, $discountAmount, $discount);
        }

        return $this->order;
    }
}
