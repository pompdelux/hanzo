<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\BasketBundle\Service;

use Hanzo\Bundle\PaymentBundle\PaymentActionsProxy;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Cleanup
 *
 * @package Hanzo\Bundle\BasketBundle\Service
 */
class Cleanup
{
    /**
     * @var OutputInterface
     */
    private $outputInterface;

    /**
     * @var string
     */
    private $trigger;

    /**
     * @var PaymentActionsProxy
     */
    private $paymentActionsProxy;

    /**
     * @var FallbackCleanupHandler
     */
    private $fallbackCleanupHandler;

    /**
     * constructor
     *
     * @param PaymentActionsProxy    $paymentActionsProxy
     * @param FallbackCleanupHandler $fallbackCleanupHandler
     */
    public function __construct(PaymentActionsProxy $paymentActionsProxy, FallbackCleanupHandler $fallbackCleanupHandler)
    {
        $this->paymentActionsProxy    = $paymentActionsProxy;
        $this->fallbackCleanupHandler = $fallbackCleanupHandler;
    }

    /**
     * @param OutputInterface $outputInterface
     */
    public function setOutputInterface(OutputInterface $outputInterface)
    {
        $this->outputInterface = $outputInterface;
        $this->fallbackCleanupHandler->setOutputInterface($outputInterface);
    }

    /**
     * @param string $name
     */
    public function setTrigger($name)
    {
        $this->trigger = $name;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function run()
    {
        if (empty($this->trigger)) {
            throw new \InvalidArgumentException('You must set a trigger - this is needed to document where deletes come from.');
        }

        if (!defined('ACTION_TRIGGER')) {
            define('ACTION_TRIGGER', $this->trigger);
        }

        /** @var Orders $order */
        foreach ($this->findOrders() as $order) {
            if ($order->getInEdit()) {
                $this->handleInEdit($order);
            } else {
                $this->handleAbandoned($order);
            }
        }
    }


    /**
     * @return array|mixed|\PropelObjectCollection
     */
    protected function findOrders()
    {
        return OrdersQuery::create()
            ->filterByState(['max' => Orders::STATE_PAYMENT_OK])
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), \Criteria::LESS_THAN)
            ->filterByCreatedAt(date('Y-m-d H:i:s', strtotime('6 month ago')), \Criteria::GREATER_THAN)
            ->find();
    }

    /**
     * @param Orders $order
     *
     * @return mixed
     */
    protected function handleInEdit(Orders $order)
    {
        $api = $this->paymentActionsProxy->getApiByName(strtolower($order->getBillingMethod().'api'));

        if ((!$api) || (!method_exists($api, 'handleStaleEdits'))) {
            return $this->fallbackCleanupHandler->handleStaleEdits($order);
        }

        return $api->handleStaleEdits($order, $this->outputInterface);
    }

    /**
     * @param Orders $order
     *
     * @return mixed
     */
    protected function handleAbandoned(Orders $order)
    {
        $api = $this->paymentActionsProxy->getApiByName(strtolower($order->getBillingMethod().'api'));

        if ((!$api) || (!method_exists($api, 'handleAbandoned'))) {
            return $this->fallbackCleanupHandler->handleAbandoned($order);
        }

        return $api->handleAbandoned($order, $this->outputInterface);
    }
}
