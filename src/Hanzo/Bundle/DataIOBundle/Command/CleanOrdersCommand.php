<?php /* vim: set sw=4: */

/**
 * usage:
 *  - run every 2 hours
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:dataio:clean_orders
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:dataio:clean_orders
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:dataio:clean_orders
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanOrdersCommand
 *
 * @package Hanzo\Bundle\DataIOBundle
 */
class CleanOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:clean_orders')
            ->setDescription('Delete orders in a stale mode')
            ->addOption('dry_run', null, InputOption::VALUE_NONE, 'If set, the task will not change any orders');
    }

    /**
     * executes the job
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // used in model to track deletes
        define('ACTION_TRIGGER', $this->getName());

        $container = $this->getContainer();

        $dryRun = $input->getOption('dry_run');
        $cleanupManager = $container->get('cleanup_manager');

        $cancelCount = $cleanupManager->cancelStaleOrderEdit($container, $dryRun);
        $deleteCount = $cleanupManager->deleteStaleOrders($dryRun);

        if ($dryRun) {
            error_log("\n[".date('Y-m-d H:i:s').'] Would roll back '.$cancelCount.' stale orders.');
            error_log('['.date('Y-m-d H:i:s').'] Would delete '.$deleteCount.' stale orders.');
        }

        $prefix = substr($this->getContainer()->getParameter('locale'), -2);
        $this->getContainer()->get('pdl.phpredis.permanent')->hset('cron.log', $prefix.':clean_orders', time());
    }
}
