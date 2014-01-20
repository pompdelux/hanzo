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

        $propel_connection = \Propel::getConnection(null, \Propel::CONNECTION_WRITE);
        $propel_statement = $propel_connection->prepare("
            SELECT
                SUM(orders_lines.quantity) AS qty
            FROM
                orders_lines
            INNER JOIN
                orders
                ON (
                    orders_lines.orders_id=orders.id
                )
            WHERE
                orders_lines.products_id = :products_id
                AND
                    orders.state<40
                AND
                    orders_lines.type='product'
--                AND
--                    orders.updated_at>'2014-01-20 11:22:59'
--                AND
--                    orders.created_at>'2013-07-20 13:22:59'
            GROUP BY
                orders_lines.products_id
            ORDER BY
                orders_lines.products_id
            LIMIT 1
        ");

        $propel_statement->bindValue('products_id', 2);
        $propel_statement->execute();
var_dump($propel_statement->fetchColumn());

        $propel_statement->bindValue('products_id', 4);
        $propel_statement->execute();
var_dump($propel_statement->fetchColumn());

        $stock = $this->getContainer()->get('stock');
        $response = $stock->decrease(ProductsQuery::create()->findOneById(2), 385);
var_dump($response);
        return;



        $products = ProductsStockQuery::create()
            ->orderByProductsId()
            ->orderByAvailableFrom()
            ->find()
        ;

        foreach ($products as $product) {
            $stock->set($product->getProductsId(), $product->getAvailableFrom(), $product->getQuantity());
        }
    }
}
