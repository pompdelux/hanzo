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
    Hanzo\Model\OrdersLines,
    Hanzo\Model\OrdersLinesPeer,
    Hanzo\Model\OrdersLinesQuery,
    Hanzo\Model\OrdersStateLog,
    Hanzo\Model\OrdersAttributes,
    Hanzo\Model\OrdersAttributesQuery,
    Hanzo\Model\OrdersVersions,
    Hanzo\Model\OrdersVersionsQuery,
    Hanzo\Model\ShippingMethods
    ;


use Exception;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:test')
            ->setDescription('For testing')
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
        $order = OrdersPeer::retrieveByPK(572871);
        $currentVersion = $order->getVersionId();

        //var_dump($order->getOrdersAttributess());
        //echo $order->getOrdersAttributess()->toArray()."\n";

        if ( !( $currentVersion < 2 ) ) // If the version number is less than 2 there is no previous version
        {
          $oldOrderVersion = ( $currentVersion - 1);
          $oldOrder = $order->getOrderAtVersion($oldOrderVersion);


          //var_dump($oldOrder->getOrdersAttributess());
          //print_r($oldOrder->getOrdersAttributess()->toArray());

          $oldOrder->cancelPayment($this->getContainer()->get('payment.paybybillapi'));
        }
    }
}
