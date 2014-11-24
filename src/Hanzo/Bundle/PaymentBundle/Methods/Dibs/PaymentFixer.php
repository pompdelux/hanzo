<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\PaymentBundle\Methods\Dibs;

use Hanzo\Bundle\AxBundle\Actions\Out\AxServiceWrapper;
use Hanzo\Bundle\AxBundle\Actions\Out\PheanstalkQueue;
use Hanzo\Bundle\CoreBundle\Service\Model\OrdersService;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersStateLog;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PaymentFixer
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\Dibs
 */
class PaymentFixer
{
    /**
     * @var DibsApi
     */
    private $dibsApi;

    /**
     * @var AxServiceWrapper
     */
    private $axServiceWrapper;

    /**
     * @var PheanstalkQueue
     */
    private $pheanstalkQueue;

    /**
     * @var OrdersService
     */
    private $ordersService;

    /**
     * @var OutputInterface
     */
    private $outputInterface = null;

    /**
     * @param AxServiceWrapper $axServiceWrapper
     * @param PheanstalkQueue  $pheanstalkQueue
     * @param OrdersService    $ordersService
     */
    public function __construct(AxServiceWrapper $axServiceWrapper, PheanstalkQueue $pheanstalkQueue, OrdersService $ordersService)
    {
        $this->axServiceWrapper = $axServiceWrapper;
        $this->pheanstalkQueue  = $pheanstalkQueue;
        $this->ordersService    = $ordersService;
    }

    /**
     * @param DibsApi $dibsApi
     */
    public function setApi(DibsApi $dibsApi)
    {
        $this->dibsApi = $dibsApi;
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
    public function resolve(Orders $order)
    {
        $attributes = $order->getAttributes();

        $transId = null;
        if (empty($attributes->payment->transact)) {
            try {
                $result  = $this->dibsApi->call()->transinfo($order);
                $transId = $result->data['transact'];
            } catch (DibsApiCallException $e) {
            }
        }

        if (is_null($transId)) {
            return $this->handleNullTransId($order);
        }

        // reset transact id
        $order->setAttribute('transact', 'payment', $transId);

        if (is_null($this->outputInterface)) {
            $order->save();
        }

        try {
            if ($this->outputInterface) {
                $this->outputInterface->writeln('Trying to get payment status from DIBS for transaction: '.$transId);
            }

            $orderStatus = $this->getStatus($order);
        } catch (DibsApiCallException $e) {
            if ($this->outputInterface) {
                $this->outputInterface->writeln(' - DIBS call failed: '.$e->getMessage());
            }

            return false;
        }

        if (isset($orderStatus->data['status'])) {
            if (2 == $orderStatus->data['status']) {
                return $this->updatePaymentInfo($order);
            }

            if ($this->outputInterface) {
                $this->outputInterface->writeln(' - DIBS Order status: "'.$orderStatus->data['status'].'" - we delete the order.');
            }

            $order->setDBConnection(\Propel::getConnection());
            $order->setIgnoreDeleteConstraints(true);

            return $this->ordersService->deleteOrder($order);
        }
    }

    /**
     * @param Orders $order
     *
     * @throws \Exception
     * @throws \PropelException
     *
     * @return mixed
     */
    private function handleNullTransId(Orders $order)
    {
        if ($order->getInEdit()) {
            if (is_null($this->outputInterface)) {
                $order->toPreviousVersion();
                $this->axServiceWrapper->SalesOrderLockUnlock($order, false);

                $log = new OrdersStateLog();
                $log->info($order->getId(), Orders::INFO_STATE_EDIT_CANCLED_BY_CLEANUP);

                return $log->save();
            }

            return $this->outputInterface->writeln(' - Should role back to prew version of order... '.implode(', ', $order->getVersionIds()));
        }

        if (is_null($this->outputInterface)) {
            $order->setDBConnection(\Propel::getConnection());
            $order->setIgnoreDeleteConstraints(true);

            return $this->ordersService->deleteOrder($order);
        }

        $this->outputInterface->writeln(' - Deleting order: '.$order->getId());
    }

    /**
     * @param Orders $order
     *
     * @return DibsApiCallResponse
     * @throws DibsApiCallException
     */
    protected function getStatus(Orders $order)
    {
        return $this->dibsApi->call()->payinfo($order);
    }

    /**
     * @param Orders $order
     *
     * @return mixed
     * @throws \Exception
     * @throws \PropelException
     */
    protected function updatePaymentInfo(Orders $order)
    {
        // Looks like payment is ok -> update order
        if ($this->outputInterface) {
            $this->outputInterface->writeln(' - Payment looks ok, updating order, state ok');
        }

        // needed now, checked in the callback
        $order->setState(Orders::STATE_PAYMENT_OK);
        $order->setFinishedAt(time());

        try {
            $callbackData = $this->dibsApi->call()->callback($order);
        } catch (DibsApiCallException $e) {
            if ($this->outputInterface) {
                $this->outputInterface->writeln(' - DIBS call failed: '.$e->getMessage());
            }

            return false;
        }

        $fields = [
            'paytype',
            'cardnomask',
            'cardprefix',
            'acquirer',
            'cardexpdate',
            'currency',
            'ip',
            'approvalcode',
            'transact',
        ];

        foreach ($fields as $field) {
            if (isset($callbackData->data[$field])) {
                if ($this->outputInterface) {
                    $this->outputInterface->writeln(' -:- Setting field '.$field.' to '.$callbackData->data[$field]);
                }

                $order->setAttribute($field, 'payment', $callbackData->data[$field]);
            }
        }

        // in debug mode we do not complete the last steps in updating the payment info on the order.
        if ($this->outputInterface) {
            return null;
        }

        if ($order->getInEdit()) {
            $currentVersion = $order->getVersionId();

            // If the version number is less than 2 there is no previous version
            if (!($currentVersion < 2)) {
                $oldOrderVersion = ($currentVersion - 1);
                $oldOrder        = $order->getOrderAtVersion($oldOrderVersion);

                try {
                    $this->dibsApi->call()->cancel($oldOrder->getCustomers(), $oldOrder);
                } catch (\Exception $e) {
                    Tools::log('DIBS: Could not cancel payment for old order, id: '.$oldOrder->getId().' error was: '.$e->getMessage());
                }
            }

            $log = new OrdersStateLog();
            $log->setOrdersId($order->getId());
            $log->setState(0);
            $log->setMessage(Orders::INFO_STATE_EDIT_CANCLED_BY_CLEANUP);
            $log->setCreatedAt(time());
            $log->save();
        }

        try {
            // let the queue system handle this
            $this->pheanstalkQueue->appendSendOrder($order);

            $order->setState(Orders::STATE_PENDING);
            $order->setInEdit(false);
            $order->setSessionId($order->getId());
            $order->save();
        } catch (\Exception $e) {
        }
    }
}
