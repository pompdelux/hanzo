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

use Hanzo\Model\Coupons;
use Hanzo\Model\CouponsQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersAttributesQuery;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersToCoupons;
use Hanzo\Model\OrdersToCouponsPeer;
use Hanzo\Model\OrdersToCouponsQuery;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Translator;

class CouponHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $translator;

    /**
     * @var Orders
     */
    private $order;

    /**
     * @var Coupons
     */
    private $coupon;

    /**
     * Construct
     *
     * @param LoggerInterface $logger
     * @param Translator      $translator
     */
    public function __construct(LoggerInterface $logger, Translator $translator)
    {
        $this->logger     = $logger;
        $this->translator = $translator;
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
        if (null === $this->getCoupon()) {
            return $this->order;
        }

        switch ($this->coupon->getAmountType()) {
            case Coupons::TYPE_AMOUNT:
                $this->applyAmount();
                break;
            case Coupons::TYPE_PERCENTAGE:
                $this->applyPercentage();
                break;
        }

        if (false === $this->coupon->getIsReusable()) {
            $this->coupon->setIsUsed(true);
            $this->coupon->save();
        }

        return $this->order;
    }


    /**
     * Apply amount based coupon to order.
     */
    protected function applyAmount()
    {
        // cleanup if the minimum purchase amount is not met.
        if ($this->clearOrderCouponDiscount()) {
            return;
        }

        $discount     = $this->coupon->getAmount();
        $coupon_label = $this->translator->trans('coupon', [], 'checkout');

        $this->order->setDiscountLine($coupon_label, -$discount, 'coupon.code');
        $this->order->setAttribute('amount', 'coupon', $discount);
        $this->order->setAttribute('text', 'coupon', $coupon_label);
        $this->addOrderToCoupon($discount);
    }


    /**
     * Cleanup coupon attributes and discount line if minimum purchase amount is not met.
     *
     * @return bool
     */
    protected function clearOrderCouponDiscount()
    {
        if ($this->order->getTotalPrice(true) < $this->coupon->getMinPurchaseAmount()) {
            $attributes = OrdersAttributesQuery::create()
                ->filterByOrdersId($this->order->getId())
                ->filterByNs('coupon')
                ->filterByCKey(['amount', 'text'], \Criteria::IN)
                ->find()
            ;

            foreach ($attributes as $attribute) {
                $this->order->removeOrdersAttributes($attribute);
            }

            $this->removeOrderToCoupon();

            $lines = new \PropelCollection();
            foreach ($this->order->getOrdersLiness() as $line) {
                if ('coupon.code' === $line->getProductsName()) {
                    continue;
                }

                if (('product' === $line->getType()) && (Coupons::TYPE_PERCENTAGE == $this->coupon->getAmountType())) {
                    $line->setPrice($line->getOriginalPrice());
                    $line->save();
                }

                $lines->append($line);
            }
            $this->order->setOrdersLiness($lines);

            return true;
        }

        return false;
    }


    /**
     * Adds percentage based coupons
     * These have to be set on a pr. product line basis.
     * And have to take in to account, products that cannot have discounts.
     */
    protected function applyPercentage()
    {
        // cleanup if the minimum purchase amount is not met.
        if ($this->clearOrderCouponDiscount()) {
            return;
        }

        // only products marked "is_discountable" will be used in the calculation.
        $lines = OrdersLinesQuery::create()
            ->select('ProductsId')
            ->filterByOrdersId($this->order->getId())
            ->filterByType('product')
            ->useProductsQuery()
                ->filterByIsDiscountable(1)
            ->endUse()
            ->find()
        ;

        $ids = [];
        foreach ($lines as $id) {
            $ids[] = $id;
        }

        foreach ($this->order->getOrdersLiness() as $line) {
            if (!in_array($line->getProductsId(), $ids)) {
                continue;
            }

            $discount = ($line->getOriginalPrice() / 100) * $this->coupon->getAmount();
            $line->setPrice($line->getOriginalPrice() - $discount);
            $line->save();
        }
    }


    /**
     * Add orders to coupon relation
     *
     * @param int $amount
     */
    protected function addOrderToCoupon($amount = 0)
    {
        $o2c = OrdersToCouponsQuery::create()
            ->filterByOrdersId($this->order->getId())
            ->filterByCouponsId($this->coupon->getId())
            ->findOne()
        ;

        if (!$o2c instanceof OrdersToCoupons) {
            $c = new OrdersToCoupons();
            $c->setCouponsId($this->coupon->getId());
            $c->setOrdersId($this->order->getId());
            $c->setAmount($amount);

            $criteria = new \Criteria();
            $criteria->add(OrdersToCouponsPeer::ORDERS_ID, $this->order->getId(), \Criteria::NOT_EQUAL);
            $collection = $this->order->getOrdersToCouponss($criteria);
            $collection->prepend($c);
            $this->order->setOrdersToCouponss($collection);
        }
    }


    /**
     * Remove orders to coupon relation
     *
     * @return int
     */
    protected function removeOrderToCoupon()
    {
        return OrdersToCouponsQuery::create()
            ->filterByOrdersId($this->order->getId())
            ->delete()
        ;
    }


    /**
     * Get coupon related to the order, if any.
     *
     * @return \Hanzo\Model\Coupons|null
     */
    protected function getCoupon()
    {
        $attributes = $this->order->getAttributes();

        if (!isset($attributes->coupon, $attributes->coupon->code)) {
            return;
        }

        $this->coupon = CouponsQuery::create()
            ->findOneByCode($attributes->coupon->code)
        ;

        return $this->coupon;
    }
}
