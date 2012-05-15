<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface
    ;

use Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\OrdersQuery,
    Hanzo\Model\OrdersAttributes,
    Hanzo\Model\OrdersAttributesQuery
    ;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

use Exception;

class CleanDeadOrdersCommand extends ContainerAwareCommand
{
    protected $dibsApi = null;

    protected function configure()
    {
        $this->setName('hanzo:dataio:clean_dead_orders')
            ->setDescription('Removes stale and dead orders')
            ;
    }

    /**
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dibsApi = $this->getContainer()->get('payment.dibsapi');

        $toBeDeleted = array();

        $orders = $this->getOrders();

        foreach ($orders as $order) 
        {
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
                    continue;  
                }
            }

            if ( is_null($transId) )
            {
                $toBeDeleted[] = $order;
                continue;
            }

            $order->setAttribute( 'transact', 'payment', $transId );
            $order->save();

            try
            {
                $status = $this->getStatus( $order );
            }
            catch (DibsApiCallException $e)
            {
                continue;  
            }

            if ( isset($status->data['status']) )
            {
                switch ($status->data['status']) 
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
                            continue;  
                        }

                        foreach ($fields as $field)
                        {
                            $order->setAttribute( $field , 'payment', $callbackData->data[$field] );
                        }
                        $order->save();
                        $this->getContainer()->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));
                        break;

                    default:
                        $toBeDeleted[] = $order;
                        continue;
                        break;
                } 
            }
        }

        $this->deleteOrders( $toBeDeleted );
    }

    /**
     * deleteOrders
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function deleteOrders( Array $toBeDeleted )
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
    public function getStatus( Orders $order )
    {
        return $this->dibsApi->call()->payinfo($order);
    }

    /**
     * getTransactionId
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getTransactionId( Orders $order )
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
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getOrders()
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
