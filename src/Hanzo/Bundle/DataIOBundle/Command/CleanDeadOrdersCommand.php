<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface
    ;

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
