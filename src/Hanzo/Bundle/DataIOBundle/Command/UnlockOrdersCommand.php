<?php /* vim: set sw=4: */

/**
 * usage:
 *  - run every 3 hours
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:dataio:unlock_orders
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:dataio:unlock_orders
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:dataio:unlock_orders
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Criteria;
use Propel;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;

class UnlockOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:unlock_orders')
            ->setDescription('Unlocks orders in a stale edit mode')
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
        Propel::setForceMasterConnection(true);

        $orders = OrdersQuery::create()
            ->filterByInEdit(true)
            ->filterByUpdatedAt(array('max' => '-3 hours'))
            ->filterByState(Orders::STATE_PENDING, Criteria::LESS_THAN)
            ->find()
        ;

        foreach ($orders as $order) {
            $order->toPreviousVersion();
            $this->getContainer()->get('ax_manager')->lockUnlockSalesOrder($order, false);
        }

        // should _not_ be necessary, but...
        $orders = OrdersQuery::create()
            ->filterByInEdit(true)
            ->filterByState(array(
                Orders::STATE_BEING_PROCESSED,
                Orders::STATE_SHIPPED,
            ))
            ->find()
        ;

        foreach ($orders as $order) {
            $order->setInEdit(false);
            $order->save();
        }

        Propel::setForceMasterConnection(false);
        // $q = Propel::getConnection()->getLastExecutedQuery();
        // $output->writeln('<info>'.$q.'</info>');
    }
}
