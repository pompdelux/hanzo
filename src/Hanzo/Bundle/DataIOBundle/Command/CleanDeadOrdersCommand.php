<?php /* vim: set sw=4: */

/**
 * usage:
 *     php /path/to/symfony/console --env=prod_dk --quiet hanzo:dataio:clean_dead_orders
 *     php /path/to/symfony/console --env=prod_no --quiet hanzo:dataio:clean_dead_orders
 *     php /path/to/symfony/console --env=prod_se --quiet hanzo:dataio:clean_dead_orders
 */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        $deadOrderBuster = $this->getContainer()->get('deadorder_manager');
        $deadOrderBuster->autoCleanup();
    }
}
