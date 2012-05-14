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
        $orders = $this->getOrders();
        foreach ($orders as $order) 
        {
            $transId = $this->getTransactionId($order);
        }
    }

    /**
     * getTransactionId
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getTransactionId($order)
    {
        $atts       = $order->getAttributes();
        $hasTransId = false;
        $transId    = null;

        foreach ($atts as $att)
        {
            if ( isset($att->transact) )
            {
                $hasTransId = true;
                $transId = $att->transact;
            }
        }

        if ( !$hasTransId )
        {
            $result = $this->dibsApi->call()->transinfo($order);
            print_r($result);
        }
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
