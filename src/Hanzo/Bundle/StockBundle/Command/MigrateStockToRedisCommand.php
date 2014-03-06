<?php

namespace Hanzo\Bundle\StockBundle\Command;

use Hanzo\Core\Tools;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateStockToRedisCommand extends ContainerAwareCommand
{
    protected $errors = [];

    protected function configure()
    {
        $this->setName('hanzo:stock:migrate-to-redis')
            ->setDescription('Migrate stock to redis from db.')
            ->addOption('fake-stock', 'f', InputOption::VALUE_NONE, 'If set, we will fake stock for the products.')
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

        if ($input->getOption('fake-stock')) {
            $output->writeln('faking stock..');
            $products = ProductsQuery::create()
                ->select('Id')
                ->find()
            ;

            foreach ($products as $id) {
                $stock->setLevel((int) $id, date('Y-m-d', strtotime('-2 days')), 100);
            }
            return;
        }

        $output->writeln('using stock from db..');
        $products = ProductsStockQuery::create()
            ->orderByProductsId()
            ->orderByAvailableFrom()
            ->find()
        ;

        foreach ($products as $product) {
            $stock->setLevel($product->getProductsId(), $product->getAvailableFrom(), $product->getQuantity());
        }
    }
}
