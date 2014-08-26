<?php

namespace Hanzo\Bundle\StockBundle\Command;

use Hanzo\Model\ProductsQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckStockLevelCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('hanzo:stock:check-level')
            ->setDescription('Allows for easy stock overview of an entire style.')
            ->addArgument('sku', InputArgument::REQUIRED, 'Master SKU to lookup, must be the master record.')
        ;
    }

    /**
     * executes the job
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stock = $this->getContainer()->get('stock');

        $products = ProductsQuery::create()
            ->filterByMaster($input->getArgument('sku'))
            ->orderBySku()
            ->find()
        ;

        $stock->prime($products);

        $table = $this->getHelper('table');
        $table->setHeaders(['SKU', 'date', 'quantity']);

        foreach ($products as $product) {
            foreach ($stock->get($product, true) as $level) {
                if (!is_array($level)) {
                    continue;
                }

                $table->addRow([
                    $product->getSku(),
                    $level['date'],
                    $level['quantity']
                ]);
            }
        }

        $table->render($output);
    }
}
