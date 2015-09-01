<?php

namespace Hanzo\Bundle\StockBundle;

use Hanzo\Bundle\AdminBundle\Event\FilterCategoryEvent;
use Hanzo\Core\PropelReplicator;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Stock
 *
 * @package Hanzo\Bundle\StockBundle
 */
class Stock
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $stock = [];

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var Warehouse
     */
    protected $warehouse;

    /**
     * @var PropelReplicator
     */
    protected $replicator;

    /**
     * @param string                   $locale
     * @param EventDispatcherInterface $eventDispatcher
     * @param Warehouse                $warehouse
     * @param PropelReplicator         $replicator
     */
    public function __construct($locale, EventDispatcherInterface $eventDispatcher, Warehouse $warehouse, PropelReplicator $replicator)
    {
        $this->locale          = $locale;
        $this->eventDispatcher = $eventDispatcher;
        $warehouse->setLocation($locale);
        $this->warehouse  = $warehouse;
        $this->replicator = $replicator;
    }


    /**
     * Change warehouse location, please be careful with this!
     *
     * @param string $locale
     */
    public function changeLocation($locale)
    {
        $this->warehouse->setLocation($locale);
    }


    /**
     * fetch the stock put of db and save it in a static var
     *
     * @param array $products an array of product object
     *
     * @return array
     */
    protected function load($products)
    {
        if (!is_array($products) && (!$products instanceof \PropelObjectCollection)) {
            $products = [$products];
        }

        $ids = [];
        foreach ($products as $product) {
            if (is_object($product) && method_exists($product, 'getId')) {
                $id = $product->getId();
            } else {
                $id = (int) $product;
            }

            if (isset($this->stock[$id])) {
                continue;
            }

            $ids[] = $id;
        }

        if (empty($ids)) {
            return;
        }

        foreach ($this->warehouse->getInventory($ids) as $id => $status) {
            $this->stock[$id] = $status;
        }

        // hf@bellcom.dk: ensure that lowest date comes first
        ksort($this->stock[$id]);
    }


    /**
     * load a collection of products stock to be tested in a loop or the like.
     *
     * @param array $products an array of product objects
     *
     * @return void
     */
    public function prime($products)
    {
        $this->load($products);
    }


    /**
     * check whether or not a product is in stock or not.
     *
     * @param mixed $product  A product object or product id
     * @param int   $quantity The quantity to check against
     *
     * @return mixed True if the product is available now, a DateTime object if it is available in the future, false if not in stock
     */
    public function check($product, $quantity = 1)
    {
        if (is_object($product)) {
            $id = $product->getId();
        } else {
            $id = (int) $product;
        }

        if (empty($this->stock[$id])) {
            $this->load($product);
        }

        $sum = 0;
        $now = date('Ymd');

        foreach ($this->stock[$id] as $date => $stock) {
            // the total field is just to be skipped, but should never be removed.
            if ($date === 'total') {
                continue;
            }

            $sum += $stock['quantity'];
            if ($stock['quantity'] >= $quantity) {
                // we return the date the product is available if it is later thant now, otherwise we just return true
                return $date > $now
                    ? new \DateTime($date)
                    : true
                ;
            }
        }

        return false;
    }

    /**
     * get total stock for a product
     *
     * @param mixed   $product a product object or product id
     * @param boolean $asObject
     *
     * @return mixed
     */
    public function get($product, $asObject = false)
    {
        if (is_object($product)) {
            $id = $product->getId();
        } else {
            $id = (int) $product;
        }

        if (empty($this->stock[$id])) {
            $this->load($product);
        }

        if ($asObject) {
            return $this->stock[$id];
        }

        return $this->stock[$id]['total'];
    }


    /**
     * Set the stock level on a product in the current warehouse.
     *
     * @param integer $productId
     * @param string  $date
     * @param int     $quantity
     */
    public function setLevel($productId, $date, $quantity = 0)
    {
        $this->warehouse->setInventoryRecord($productId, $date, $quantity);
    }


    /**
     * Set the stock level on a product in the current warehouse.
     * This uses an array of inventory records to update in one atomic push.
     *
     * Note: This method cleans out any records not in the supplied data set.
     *
     * @param integer $productId
     * @param array   $data
     *
     * @example $data format
     * [
     *    'xxxx' => [
     *        'date'     => '2001-01-01',
     *        'quantity' => 123,
     *    ],
     * ]
     */
    public function setLevels($productId, $data)
    {
        $this->warehouse->setInventoryRecords($productId, $data);
    }


    /**
     * decrease the stock level for a product
     *
     * @param Products|int $product  a Products object or product id
     * @param int          $quantity the quantity by which to decrease
     *
     * @return mixed, the expected delivery date on success, false otherwise.
     */
    public function decrease($product, $quantity = 1)
    {
        // nothing to do here..
        if ($quantity < 1) {
            return true;
        }

        $stock = $this->get($product, true);
        ksort($stock);
        $total = array_shift($stock);

        // return FALSE if we do not have enough in stock
        if ($total < $quantity) {
            return false;
        }

        $left      = $quantity;
        $productId = $product->getId();

        while ($left > 0) {
            $current = array_shift($stock);

            if ($current['quantity'] <= $left) {
                $this->warehouse->deleteInventoryRecord($productId, $current['date']);
            } else {
                $this->warehouse->setInventoryRecord($productId, $current['date'], $current['quantity'] - $left);
            }

            $left = $left - $current['quantity'];
        }

        // NICETO: move all db stuff to event listeners
        if ($total == $quantity) {
            $this->setStockStatus(true, $product);
            $this->warehouse->removeProductFromInventory($productId);

            // find out if the whole style is out of stock
            // if so, tag it so and fire an event (for caching n' stuff)
            if (false === $this->checkStyleStock($product)) {
                $master = ProductsQuery::create()->findOneBySku($product->getMaster());
                $this->setStockStatus(true, $master);
                $this->eventDispatcher->dispatch('product.stock.zero', new FilterCategoryEvent($master, $this->locale));
            }
        }

        unset($this->stock[$product->getId()]);

        return $current['date'];
    }


    /**
     * Returns the current quantity of reserved products
     *
     * @param int $productId
     *
     * @return int
     */
    public function getProductReservations($productId)
    {
        $sql = "
            SELECT
                SUM(orders_lines.quantity) AS qty
            FROM
                orders_lines
            INNER JOIN
                orders
                ON (
                    orders_lines.orders_id = orders.id
                )
            WHERE
                orders_lines.products_id = ".$productId."
                AND
                    orders.state < 40
                AND
                    orders.updated_at > '".date('Y-m-d H:i:s', strtotime('2 hours ago'))."'
            GROUP BY
                orders_lines.products_id
            LIMIT 1
        ";

        $results = $this->replicator->executeQuery($sql, [], $this->warehouse->getRelatedDatabases());

        $res = 0;
        foreach ($results as $result) {
            if ($record = $result->fetch(\PDO::FETCH_ASSOC)) {
                $res += $record['qty'];
            }
        }

        return $res;
    }


    /**
     * Figure out whether or not a whole style is out of stock.
     *
     * @param Products|string $query
     * @param bool            $returnCount
     *
     * @return bool
     */
    public function checkStyleStock($query, $returnCount = false)
    {
        if ($query instanceof Products) {
            $query = $query->getMaster();
        }

        $ids = ProductsQuery::create()
            ->select('Id')
            ->filterByMaster($query)
            ->find()
            ->getData();

        $this->load($ids);
        $totalStock = 0;

        foreach ($ids as $id) {
            $totalStock += $this->get($id);
        }

        if ($returnCount) {
            return $totalStock;
        }

        return (boolean) $totalStock;
    }


    /**
     * Remove stock from style
     *
     * @param Products $product
     */
    public function flushStyle(Products $product)
    {
        $items = ProductsQuery::create()
            ->select('Id')
            ->filterByMaster($product->getSku())
            ->find();

        $ids = [];
        foreach ($items as $id) {
            $this->warehouse->removeProductFromInventory($id);
            $ids[] = $id;
        }

        $this->setStockStatus(true, $ids);
    }


    /**
     * Updates the stock status across databases.
     * This really should be moved to an event listener, as it is duplicated in ECommerceServices
     *
     * @param boolean        $isOut
     * @param Products|array $product
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function setStockStatus($isOut, $product)
    {
        if ($product instanceof Products) {
            $product = [$product->getId()];
        }

        if (!is_array($product)) {
            throw new \InvalidArgumentException('$product parameter not an array or instance of Products');
        }

        $sql = "
            UPDATE
                products
            SET
                is_out_of_stock = ".(int) $isOut.",
                updated_at = NOW()
            WHERE
                id IN (".implode(',', $product).")
        ";

        return $this->replicator->executeQuery($sql, [], $this->warehouse->getRelatedDatabases());
    }
}
