<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools
    ;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApi;

use Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\OrdersQuery,
    Hanzo\Model\OrdersAttributes,
    Hanzo\Model\OrdersAttributesQuery
    ;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException;

class DeadOrderService
{
    protected $parameters;
    protected $settings;

    public function __construct($parameters, $settings)
    {
        $this->dibsApi = $parameters[0];
        $this->parameters = $parameters;
        $this->settings = $settings;

        if (!$this->dibsApi instanceof DibsApi) {
            throw new \InvalidArgumentException('DibsApi expected as first parameter.');
        }
    }

    /**
     * autoCleanup
     *
     * Finds and deletes all orders that are dead
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function autoCleanup()
    {
        $toBeDeleted = array();
        $toBeDeleted = $this->getOrdersToBeDeleted();
        $this->deleteOrders( $toBeDeleted );
    }

    /**
     * getOrdersToBeDeleted
     * @param int $limit
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getOrdersToBeDeleted( $limit = 0 )
    {
        $toBeDeleted = array();

        $orders = $this->getOrders( $limit );

        foreach ($orders as $order) 
        {
            $status = $this->checkOrderForErrors($order);
            if ( $status['is_error'] )
            {
                $toBeDeleted[] = $order;
            }
        }

        return $toBeDeleted;
    }

    /**
     * checkOrderForErrors
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function checkOrderForErrors( $order )
    {
        $status = array(
            'is_error'          => false,
            'error_msg'         => '',
            'id'                => $order->getId(),
            'order_last_update' => $order->getUpdatedAt()
        );

        $pgId = $order->getPaymentGatewayId();
        //echo "Processing: ".$order->getId()." (".$pgId.")\n";

        $transId = $this->getTransactionId($order);

        if ( is_null($pgId) && !is_null($transId) )
        {
            // No payment gateway id, but an transId
            try
            {
                $callbackData = $this->dibsApi->call()->callback($order);
                if ( isset($callbackData['orderid']) )
                {
                    $order->setPaymentGatewayId($callbackData['orderid']);
                    $pgId = $callbackData['orderid'];
                }
            }
            catch (DibsApiCallException $e)
            {
                $status['is_error'] = true;
                $status['error_msg'] = $e->getMessage();
                return $status;
            }
        }

        if ( is_null($transId) )
        {
            $status['is_error'] = true;
            $status['error_msg'] = 'Slet: Ingen transaktions id kunne findes';
            return $status;
        }

        $order->setAttribute( 'transact', 'payment', $transId );
        $order->save();

        try
        {
            $orderStatus = $this->getStatus( $order );
            error_log(__LINE__.':'.__FILE__.' '.print_r($orderStatus,1)); // hf@bellcom.dk debugging
        }
        catch (DibsApiCallException $e)
        {
            $status['is_error'] = true;
            $status['error_msg'] = $e->getMessage();
            return $status;
        }

        if ( isset($orderStatus->data['status']) )
        {
            switch ($orderStatus->data['status']) 
            {
                case 2:
                    // Looks like payment is ok -> update order
                    $order->setState( Orders::STATE_PAYMENT_OK );
                    $order->setFinishedAt(time());

                    $fields = array(
                        'paytype',
                        'cardnomask',
                        'cardprefix',
                        'acquirer',
                        'cardexpdate',
                        'currency',
                        'ip',
                        'approvalcode',
                        'transact',
                    );

                    try
                    {
                        $callbackData = $this->dibsApi->call()->callback($order);
                    }
                    catch (DibsApiCallException $e)
                    {
                        $status['is_error'] = true;
                        $status['error_msg'] = $e->getMessage();
                        return $status;
                    }

                    foreach ($fields as $field)
                    {
                        $order->setAttribute( $field , 'payment', $callbackData->data[$field] );
                    }
                    $order->save();
                    $this->getContainer()->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
                    break;

                default:
                    $status['is_error'] = true;
                    $status['error_msg'] = 'Order status er '. $orderStatus->data['status'];
                    return $status;
                    break;
            } 
        }

        return $status;
    }

    /**
     * deleteOrders
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function deleteOrders( Array $toBeDeleted )
    {
        foreach ($toBeDeleted as $order) 
        {
            //echo "Deleting order: ".$order->getId()."\n";
            $order->delete();
        }
    }

    /**
     * getStatus
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function getStatus( Orders $order )
    {
        return $this->dibsApi->call()->payinfo($order);
    }

    /**
     * getTransactionId
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function getTransactionId( Orders $order )
    {
        $atts = $order->getAttributes();

        foreach ($atts as $att)
        {
            if ( isset($att->transact) )
            {
                return $att->transact;
            }
        }

        try
        {
            $result = $this->dibsApi->call()->transinfo($order);
        }
        catch (DibsApiCallException $e)
        {
            return null;
        }

        return $result->data['transact'];
    }


    /**
     * getOrders
     *
     * Get all orders that are older than 3 hours, which have not been finished, payed via dibs, and have not reached a state heigher than 0
     *
     * NICETO: priority: low, support limit
     * @param int $limit
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getOrders( $limit = 0 )
    { 
        $orders = OrdersQuery::create()
            ->filterByUpdatedAt(array('max'=>strtotime('3 hours ago')))
            ->filterByFinishedAt(null)
            ->filterByBillingMethod('dibs')
            ->filterByState(array('max'=>0))
            ->find();

        return $orders;
    }
}
