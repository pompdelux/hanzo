<?php

namespace Hanzo\Bundle\StockBundle\Command;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateStockToRedisCommand extends ContainerAwareCommand
{
    protected $errors = [];

    protected function configure()
    {
        $this->setName('hanzo:stock:migrate-to-redis')
            ->setDescription('Migrate stock to redis from db.')
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
