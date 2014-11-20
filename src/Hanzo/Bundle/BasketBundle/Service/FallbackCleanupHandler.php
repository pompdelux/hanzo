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

use Hanzo\Bundle\AxBundle\Actions\Out\AxServiceWrapper;
use Hanzo\Bundle\CoreBundle\Service\Model\OrdersService;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersStateLog;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FallbackCleanupHandler
 *
 * @package Hanzo\Bundle\BasketBundle\Service
 */
class FallbackCleanupHandler
{
    /**
     * @var AxServiceWrapper
     */
    private $axServiceWrapper;

    /**
     * @var OutputInterface
     */
    private $outputInterface;

    /**
     * @param AxServiceWrapper $axServiceWrapper
     * @param OrdersService    $ordersService
     */
    public function __construct(AxServiceWrapper $axServiceWrapper, OrdersService $ordersService)
    {
        $this->axServiceWrapper = $axServiceWrapper;
        $this->ordersService = $ordersService;
    }

    /**
     * @param OutputInterface $outputInterface
     */
    public function setOutputInterface(OutputInterface $outputInterface)
    {
        $this->outputInterface = $outputInterface;
    }

    /**
     * @param Orders $order
     *
     * @return mixed
     */
    public function handleStaleEdits(Orders $order)
    {
        if ($this->outputInterface) {
            return $this->outputInterface->writeln('Order: #'.$order->getId().' would be roled back one version and unlocked in AX.');
        }

        $order->toPreviousVersion();
        $this->axServiceWrapper->SalesOrderLockUnlock($order, false);

        $log = new OrdersStateLog();
        $log->info($order->getId(), Orders::INFO_STATE_EDIT_CANCLED_BY_CLEANUP);
        $log->save();
    }

    /**
     * @param Orders $order
     *
     * @return mixed
     */
    public function handleAbandoned(Orders $order)
    {
        $attributes = $order->getAttributes();

        // only delete orders which has no payment info attached
        if (empty($attributes->payment) || empty($attributes->payment->transact)) {
            if ($this->outputInterface) {
                return $this->outputInterface->writeln('Order: #'.$order->getId().' will be deleted, state is: '.$order->getState());
            }

            $order->setIgnoreDeleteConstraints(true);
            $order->setDBConnection(\Propel::getConnection());
            $this->ordersService->deleteOrder($order);
        }
    }
}
