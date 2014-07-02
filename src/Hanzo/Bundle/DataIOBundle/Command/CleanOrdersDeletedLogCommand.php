<?php /* vim: set sw=4: */

/**
 * usage:
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:dataio:clean_deleted_orders_log
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:dataio:clean_deleted_orders_log
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:dataio:clean_deleted_orders_log
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Criteria;
use Propel;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Model\OrdersDeletedLogQuery;

class CleanOrdersDeletedLogCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:dataio:clean_deleted_orders_log')
            ->setDescription('Delete logs older than three months')
            ->addOption('dry_run', null, InputOption::VALUE_NONE, 'If set, the task will not delete any logs')
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
        $dry_run = $input->getOption('dry_run');
        $query = OrdersDeletedLogQuery::create()->filterByDeletedAt(array('max' => strtotime("-3 months")));

        if ($dry_run) {
            $num_deleted = $query->count();
            error_log("\n[".date('Y-m-d H:i:s').'] Would delete '.$num_deleted.' logs.');
        } else {
            $query->delete();
        }

        $prefix = substr($this->getContainer()->getParameter('locale'), -2);
        $this->getContainer()->get('pdl.phpredis.permanent')->hset('cron.log', $prefix.':clean_deleted_orders_log', time());
    }
}
