<?php

namespace Hanzo\Bundle\AxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class StressTestAxCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:ax:stress-test')
            ->setDescription('Stress test AX by sending loads of orders. To ramp up, start more processes.')
            ->addArgument('order_id', InputArgument::REQUIRED, 'Id of order to use in test.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('order_id');

        $progress = $this->getHelperSet()->get('progress');
        $progress->start($output);
        $output->writeln('Stress test initiated!');

        $command = 'php '.$this->getContainer()->getParameter('kernel.root_dir').'/console hanzo:ax:resync-orders --process-isolation --quiet --no-debug --env='.$input->getOption('env').' '.$id;
        while (true) {
            $process = new Process($command);
            $process->disableOutput();
            $process->run();

            $progress->advance();
        }
    }
}
