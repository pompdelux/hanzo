<?php

namespace Hanzo\Bundle\AdminBundle\Exporter;

use Hanzo\Model\ProductsQuery;

/**
 * Class LanguageExporter
 *
 * @package Hanzo\Bundle\AdminBundle\Exporter
 */
class LanguageExporter
{
    /**
     * @param \PropelPDO|\PDO $connection
     */
    public function setDBConnection($connection)
    {
        $this->dbConnection = $connection;
    }

    /**
     * @return \PDO|\PropelPDO
     */
    private function getDBConnection()
    {
        return $this->dbConnection;
    }

    /**
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getDataAsCsv()
    {
        if (is_null($this->getDBConnection())) {
            throw new \OutOfBoundsException("Database connection needs to be set.");
        }

        $parser = new \PropelCSVParser();
        $parser->delimiter = ';';

        return $parser->toCSV($this->build(), true, false);
    }

    /**
     * @return array
     */
    private function build()
    {
        $data = [];

        $products = ProductsQuery::create()
            ->filterByMaster(null, \Criteria::ISNULL)
            ->select(['Id', 'Sku'])
            ->find();

        $data[] = ['Id', 'Sku', 'Title'];

        foreach ($products as $product) {
            if (!$product) {
                continue;
            }
            $data[] = $product;
        }

        return $data;
    }

} // END class LanguageExporter
