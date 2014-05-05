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
use Hanzo\Model\Orders;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Psr\Log\LoggerInterface;

class PersonalDiscountHandler
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Orders
     */
    private $order;


    /**
     * @param Logger $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param  Orders $order
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

        // apply group and private discounts if discounts is not disabled
        if (0 == Hanzo::getInstance()->get('webshop.disable_discounts')) {
            if ($customer->getDiscount()) {
                $discount_label = 'discount.private';
                $discount = $customer->getDiscount();
            } else {
                if ($customer->getGroups()) {
                    $discount_label = 'discount.group';
                    $discount = $customer->getGroups()->getDiscount();
                }
            }
        }

        // we do not stack discounts, so we need to recalculate the order lines
        if ($discount <> 0.00) {
            $lines = $this->order->getOrdersLiness();

            $product_ids = array();
            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $product_ids[] = $line->getProductsId();
                }
            }
            $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);

            $total = 0;
            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $price = $prices[$line->getProductsId()];

                    $line->setPrice($price['normal']['price']);
                    $line->setVat($price['normal']['vat']);
                    $line->setOriginalPrice($price['normal']['price']);

                    $total += ($line->getPrice() * $line->getQuantity());
                }
            }

            // so far _all_ discounts are handled as % discounts
            $discount_amount = ($total / 100) * $discount;
            $this->order->setDiscountLine($discount_label, $discount_amount, $discount);
        }

        return $this->order;
    }
}
