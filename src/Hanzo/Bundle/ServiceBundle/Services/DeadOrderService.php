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

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException;

class DeadOrderService
{
    protected $parameters;
    protected $settings;

    protected $dryrun = false;
    protected $debug  = false;

    public function __construct($parameters, $settings)
    {
        $this->dibsApi = $parameters[0];
        $this->eventDispatcher = $parameters[1];
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
     * @param bool $dryrun Avoid changing orders
     * @param bool $debug Output debugging info
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function autoCleanup( $dryrun, $debug )
    {
        $this->dryrun = $dryrun;
        $this->debug  = $debug;

        $this->debug("Starting DeadOrderService Auto Clean");

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

        $this->debug("Found ".count($orders)." that matches filter");

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
        $this->debug("Processing: order id: ".$order->getId()." (payment gateway id:".$pgId."), in state: ".$order->getState());

        $transId = $this->getTransactionId($order);

        if ( empty($pgId) && !is_null($transId) )
        {
            // No payment gateway id, but an transId
            $this->debug("Has no payment gateway id, but an transId: ". $transId);
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
        $this->debug("Setting transId: ". $transId);

        if ( !$this->dryrun )
        {
          $order->save();
        }

        try
        {
            $orderStatus = $this->getStatus( $order );
            $this->debug( "Order status by dibs, desc: ". $orderStatus->data['status_description'] .' status: '. $orderStatus->data['status'] );
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
                    $this->debug( "Payment looks ok, updating order, state ok");
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
                        $this->debug( "Setting field ".$field." to ".$callbackData->data[$field]);
                        $order->setAttribute( $field , 'payment', $callbackData->data[$field] );
                    }

                    if ( !$this->dryrun )
                    {
                      $order->save();
                      $this->debug( "Dispatching order.payment.collected event" );
                      $this->eventDispatcher->dispatch('order.payment.collected', new FilterOrderEvent($order));
                    }
                    else
                    {
                      $this->debug( "Not dispatching order.payment.collected event (dryrun)" );
                    }
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
            $this->debug("Deleting order: ".$order->getId());
            $order->delete();
        }
    }

    /**
     * debug
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function debug( $msg )
    {
      $this->debug ? error_log('[DEBUG]: '.$msg) : '';
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
            //->filterByFinishedAt(null)
            ->filterByBillingMethod('dibs')
            ->filterByState(array( 'max' => Orders::STATE_PAYMENT_OK) )
            ->find();

        return $orders;
    }
}
