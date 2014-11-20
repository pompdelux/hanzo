<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\PaymentBundle;

use Hanzo\Core\Tools;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Orders;

/**
 * Class PaymentActionsProxy
 *
 * @package Hanzo\Bundle\PaymentBundle
 */
class PaymentActionsProxy
{
    private $paymentApis = [];

    /**
     * @param \SplDoublyLinkedList $services
     */
    public function __construct(\SplDoublyLinkedList $services)
    {
        foreach ($services as $service) {
            $name = strtolower(trim(strrchr(get_class($service), '\\'), '\\'));
            $this->paymentApis[$name] = $service;
        }
    }

    /**
     * @param Orders $order
     *
     * @throws \Exception
     */
    public function cancelPayment(Orders $order)
    {
        if (($order->getState() > Orders::STATE_PENDING)) {
            throw new \Exception('Not possible to cancel payment on an order in state "'.$order->getState().'"');
        }

        $paymentMethod = $order->getBillingMethod();

        // handle old dibs payment junk
        switch ($paymentMethod)
        {
            case 'DIBS Payment Services (Credit Ca':
            case 'DIBS Betaling (Kredittkort)':
                $paymentMethod = 'dibs';
                break;
            case 'Gothia':
                $paymentMethod = 'gothia';
                break;
        }

        if (empty($paymentMethod) || !isset($this->paymentApis[$paymentMethod.'api'])) {
            return;
        }

        $api = $this->paymentApis[$paymentMethod.'api'];

        $response = null;
        $customer = CustomersQuery::create()->findOneById($order->getCustomersId(), $order->getDBConnection());

        if ($customer) {
            $response = $api->call()->cancel($customer, $order);
        }

        if (is_object($response) && $response->isError()) {
            $debug = [];
            $msg   = 'Could not cancel order';

            if (in_array($paymentMethod, ['gothia', 'gothiade'])) {
                $debug['TransactionId'] = $response->transactionId;
                $msg .= ' at Gothia (Transaction ID: '. $response->transactionId .')';
            }

            Tools::debug('Cancel payment failed', __METHOD__, ['PaymentMethod' => $paymentMethod, $debug]);
            throw new \Exception($msg);
        }

        if (!is_object($response)) {
            $msg = 'Could not cancel order #'.$order->getId();
            Tools::debug('Cancel payment failed, response is not an object', __METHOD__, ['PaymentMethod' => $paymentMethod]);
            throw new \Exception($msg);
        }
    }





    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getApiByName($name)
    {
        if (isset($this->paymentApis[$name])) {
            return $this->paymentApis[$name];
        }

        return false;
    }
}
