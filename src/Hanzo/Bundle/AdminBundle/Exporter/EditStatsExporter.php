<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AdminBundle\Exporter;

/**
 * Class EditStatsExporter
 *
 * @package Hanzo\Bundle\AdminBundle\Exporter
 */
class EditStatsExporter
{
    /**
     * @var string date (YYYY-MM-DD)
     */
    private $fromDate;
    private $toDate;

    /**
     * @var \PDO|\PropelPDO
     */
    private $dbConnection;

    /**
     * @param string $date Start date (YYYY-MM-DD)
     */
    public function setFromDate($date)
    {
        $this->fromDate = $date;
    }

    /**
     * @param string $date End date (YYYY-MM-DD)
     */
    public function setToDate($date)
    {
        $this->toDate = $date;
    }

    /**
     * @param \PDO|\PropelPDO $c
     */
    public function setDBConnection($c)
    {
        $this->dbConnection = $c;
    }

    /**
     * @return string
     */
    public function getCsvReport()
    {
        $parser = new \PropelCSVParser();
        $parser->delimiter = ';';
        $parser->quoting = \PropelCSVParser::QUOTE_ALL;

        return $parser->toCSV($this->generate(), true);
    }

    /**
     * @return array
     */
    private function generate()
    {
        if (empty($this->dbConnection)) {
            $this->dbConnection = \Propel::getConnection();
        }

        $orderData = $this->getOrderData();
        $orderData = $this->getVersionData($orderData);

        return $orderData;
    }


    /**
     * @return array
     */
    private function getOrderData()
    {
        $sql = "
            SELECT
                o.id AS order_id,
                o.version_id,
				(
				    SELECT
				        sum(orders_lines.quantity * orders_lines.price)
                    FROM
                        orders_lines
                    WHERE
                        orders_lines.orders_id = o.id
	            ) AS total

			FROM
				orders AS o
            WHERE
                o.version_id > 1
                AND (
                    o.created_at >= :fromDate
                    AND
                    o.created_at < :toDate
                )
        ";

        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute([
                'fromDate' => $this->fromDate . ' 00:00:01',
                'toDate'   => $this->toDate   . ' 23:59:59',
            ]);

        $data = [];
        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[$record['order_id']] = [
                'order_id'         => $record['order_id'],
                'current_version'  => $record['version_id'],
                'previous_version' => 1,
                'current_total'    => number_format($record['total'], 2, ',', ''),
                'previous_total'   => 0,
            ];
        }

        return $data;
    }

    /**
     * @param array $orderData
     *
     * @return array
     */
    private function getVersionData(array $orderData = [])
    {
        $sql = "
                SELECT
                    content,
                    version_id
                FROM
                    orders_versions
                WHERE
                    orders_id = :orderId
                ORDER BY
                    version_id ASC
                LIMIT 1
            ";
        $stmt = $this->dbConnection->prepare($sql);

        foreach (array_keys($orderData) as $orderId) {
            $stmt->execute(['orderId' => $orderId]);

            $record = $stmt->fetch(\PDO::FETCH_ASSOC);
            $products = unserialize($record['content'])['products'];

            $total = 0;
            foreach ($products as $product) {
                $total += ($product['Price'] * $product['Quantity']);
            }

            $orderData[$orderId]['previous_version'] = $record['version_id'];
            $orderData[$orderId]['previous_total']   = number_format($total, 2, ',', '');
        }

        return $orderData;
    }
}
