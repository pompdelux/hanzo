<?php

namespace Hanzo\Bundle\AxBundle\Command;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResyncOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:ax:resync-orders')
            ->setDescription('Allows for easy stock overview of an entire style.')
            ->addArgument('orders', InputArgument::REQUIRED, 'Comma seperated list of order ids, or filename containing order ids.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ids = $input->getArgument('orders');

        if (is_file($ids)) {
            $ids = explode(',', str_replace(["\r", "\n"], ['', ','], file_get_contents($ids)));
        } else {
            $ids = explode(',', $ids);
        }

        $ids     = array_map('trim', $ids);
        $ax      = $this->getContainer()->get('ax.out');
        $unknown = 0;

        $output->writeln('Resending '.count($ids).' orders.');
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($output, count($ids));

        foreach ($ids as $id) {
            $order = OrdersQuery::create()->findOneById($id);

            if ($order instanceof Orders) {
                $ax->sendOrder($order);
            } else {
                $unknown++;
            }

            $progress->advance();
        }

        $progress->finish();

        if ($unknown) {
            $output->writeln('<error>Note: '.$unknown." id's not recognized as orders.</error>");
        }
    }
}
