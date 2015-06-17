<?php /* vim: set sw=4: */

/**
 * usage:
 *  - run every 2 hours
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:dataio:clean_orders
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:dataio:clean_orders
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:dataio:clean_orders
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

class CleanOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:clean_orders')
            ->setDescription('Delete orders in a stale mode')
            ->addOption('dry_run', null, InputOption::VALUE_NONE, 'If set, the task will not change any orders')
        ;
    }

    /**
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // used in model to track deletes
        define('ACTION_TRIGGER', $this->getName());

        $container = $this->getContainer();

        $dry_run = $input->getOption('dry_run');
        $cleanup_manager = $container->get('cleanup_manager');

        $cancel_count = $cleanup_manager->cancelStaleOrderEdit($container, $dry_run);
        $delete_count = $cleanup_manager->deleteStaleOrders($dry_run);

        if ($dry_run) {
            error_log("\n[".date('Y-m-d H:i:s').'] Would roll back '.$cancel_count.' stale orders.');
            error_log('['.date('Y-m-d H:i:s').'] Would delete '.$delete_count.' stale orders.');
        }

        $prefix = substr($this->getContainer()->getParameter('locale'), -2);
        $this->getContainer()->get('pdl.phpredis.permanent')->hset('cron.log', $prefix.':clean_orders', time());
    }
}
