<?php /* vim: set sw=4: */

/**
 * usage:
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:dataio:clean_dead_orders
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:dataio:clean_dead_orders
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:dataio:clean_dead_orders
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanDeadOrdersCommand
 *
 * @package Hanzo\Bundle\DataIOBundle
 */
class CleanDeadOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:clean_dead_orders')
            ->setDescription('Removes stale and dead orders')
            ->addOption('dryrun', null, InputOption::VALUE_NONE, 'If set, the task will not change any orders')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'If set, output debugging info');
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

        $dryrun = false;
        if ($input->getOption('dryrun')) {
            $dryrun = true;
        }

        $debug = false;
        if ($input->getOption('debug')) {
            $debug = true;
        }

        $deadOrderBuster = $this->getContainer()->get('deadorder_manager');
        $deadOrderBuster->autoCleanup($dryrun, $debug);

        $prefix = substr($this->getContainer()->getParameter('locale'), -2);
        $this->getContainer()->get('pdl.phpredis.permanent')->hset('cron.log', $prefix.':clean_dead_orders', time());
    }
}
