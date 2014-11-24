<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

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
     * @var PensioApi
     */
    private $pensioApi;

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
     * @param PensioApi $pensioApi
     */
    public function setApi(PensioApi $pensioApi)
    {
        $this->pensioApi = $pensioApi;
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
        if (empty($attributes->payment->transaction_id)) {
            try {
                $result = $this->pensioApi->call()->getPayment($order, true);

                $transactions = $result->getXml()->xpath('//*/Transaction');
                if (isset($transactions[0])) {
                    $transaction = $transactions[0];
                }
            } catch (\Exception $e) {
            }
        }

        if (empty($transaction)) {
            return $this->handleNullTransId($order);
        }

        $transactionStatus = (string) $transaction->TransactionStatus;

        // catch any transactions not initialized or finalized
        if (!in_array($transactionStatus, ['ideal_initialized', 'bank_payment_finalized'])) {
            return $this->handleNullTransId($order);
        }

        // reset transact id
        $supportsRefundsMap = [
            'false' => 0,
            'true'  => 1,
        ];

        $order->setAttribute('transaction_id', 'payment',  $transactionStatus);
        $order->setAttribute('shop_orderid', 'payment', $order->getPaymentGatewayId());
        $order->setAttribute('payment_status', 'payment', (string) $transaction->TransactionStatus);
        $order->setAttribute('nature', 'payment', (string) $transaction->PaymentNature);
        $order->setAttribute('type', 'payment', (string) $transaction->AuthType);
        $order->setAttribute('SupportsRelease', 'payment', $supportsRefundsMap[(string) $transaction->PaymentNatureService->SupportsRelease]);
        $order->setAttribute('SupportsRefunds', 'payment', $supportsRefundsMap[(string) $transaction->PaymentNatureService->SupportsRefunds]);

        if (is_null($this->outputInterface)) {
            $order->save();
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
}
