<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\VarnishBundle\Command;

use Exception;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Core\Tools;

class PurgeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:varnish:purge')
            ->setDescription('PURGE Varnish cache.')
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
        try {
            $this->getContainer()->get('varnish.controle')->banUrl('/');
            $output->writeln('<info>Varnish PURGE send.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Could not PURGE Varnish, error was: </error><info>'.$e->getMessage().'</info>');
            Tools::log($e->getMessage());
        }
    }
}
