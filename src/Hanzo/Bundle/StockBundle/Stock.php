<?php

namespace Hanzo\Bundle\StockBundle;

use Hanzo\Core\PropelReplicator;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Hanzo\Bundle\AdminBundle\Event\FilterCategoryEvent;

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
     * @var EventDispatcher
     */
    protected $event_dispatcher;

    /**
     * @var Warehouse
     */
    protected $warehouse;

    /**
     * @var PropelReplicator
     */
    protected $replicator;

    /**
     * @param string           $locale
     * @param EventDispatcher  $event_dispatcher
     * @param Warehouse        $warehouse
     * @param PropelReplicator $replicator
     */
    public function __construct($locale, EventDispatcher $event_dispatcher, Warehouse $warehouse, PropelReplicator $replicator)
    {
        $this->locale = $locale;
        $this->event_dispatcher = $event_dispatcher;
        $warehouse->setLocation($locale);
        $this->warehouse = $warehouse;
        $this->replicator = $replicator;
    }


    /**
     * fetch the stock put of db and save it in a static var
     *
     * @param  array $products an array of product object
     * @return array
     */
    protected function load($products)
    {
        if (!is_array($products) && (!$products instanceof \PropelObjectCollection)) {
            $products = [$products];
        }

        $ids = [];
        foreach($products as $product) {
            if (is_object($product) && method_exists($product, 'getId')) {
                $id = $product->getId();
            } else {
                $id = (int) $product;
            }

            if (isset($this->stock[$id])){
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
    }


    /**
     * load a collection of products stock to be tested in a loop or the like.
     *
     * @param array $products an array of product objects
     * @return void
     */
    public function prime($products)
    {
        $this->load($products);
    }


    /**
     * check whether or not a product is in stock or not.
     *
     * @param  mixed $product   A product object or product id
     * @param  int   $quantity  The quantity to check against
     * @return mixed            True if the product is available now, a DateTime object if it is available in the future, false if not in stock
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
     * @param  mixed   $product a product object or product id
     * @param  boolean $as_object
     * @return mixed
     */
    public function get($product, $as_object = false)
    {
        if (is_object($product)) {
            $id = $product->getId();
        } else {
            $id = (int) $product;
        }

        if (empty($this->stock[$id])) {
            $this->load($product);
        }

        if ($as_object) {
            return $this->stock[$id];
        }

        return $this->stock[$id]['total'];
    }


    /**
     * Set the stock level on a product in the current warehouse.
     *
     * @param integer $product_id
     * @param string  $date
     * @param int     $quantity
     */
    public function setLevel($product_id, $date, $quantity = 0)
    {
        $this->warehouse->setInventoryRecord($product_id, $date, $quantity);
    }


    /**
     * Set the stock level on a product in the current warehouse.
     * This uses an array of inventory records to update in one atomic push.
     *
     * Note: This method cleans out any records not in the supplied data set.
     *
     * @param integer $product_id
     * @param array   $data
     *
     * $data format
     * [
     *    'xxxx' => [
     *        'date'     => '2001-01-01',
     *        'quantity' => 123,
     *    ],
     * ]
     */
    public function setLevels($product_id, $data)
    {
        $this->warehouse->setInventoryRecords($product_id, $data);
    }


    /**
     * decrease the stock level for a product
     *
     * @param \Hanzo\Model\Products $product a product object
     * @param int $quantity the quantity by which to decrease
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

        $left = $quantity;
        $product_id = $product->getId();
        while ($left > 0) {
            $current = array_shift($stock);

            if ($current['quantity'] <= $left) {
                $this->warehouse->deleteInventoryRecord($product_id, $current['date']);
            } else {
                $this->warehouse->setInventoryRecord($product_id, $current['date'], $current['quantity'] - $left);
            }

            $left = $left - $current['quantity'];
        }

        // NICETO: move all db stuff to event listeners
        if ($total == $quantity){
            $this->setStockStatus(true, $product);
            $this->warehouse->removeProductFromInventory($product_id);

            // find out if the whole style is out of stock
            // if so, tag it so and fire an event (for caching n' stuff)
            if (false === $this->checkStyleStock($product)) {
                $master = ProductsQuery::create()->findOneBySku($product->getMaster());
                $this->setStockStatus(true, $master);
                $this->event_dispatcher->dispatch('product.stock.zero', new FilterCategoryEvent($master, $this->locale));
            }
        }

        unset($this->stock[$product->getId()]);

        return $current['date'];
    }


    /**
     * Returns the current quantity of reserved products
     *
     * @param $product_id
     * @return int
     */
    public function getProductReservations($product_id)
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
                orders_lines.products_id = ".$product_id."
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
     * @param  Products|string $query
     * @return bool
     */
    public function checkStyleStock($query)
    {
        if ($query instanceof Products) {
            $query = $query->getMaster();
        }

        $ids = ProductsQuery::create()
            ->select('Id')
            ->filterByMaster($query)
            ->find()
            ->getData()
        ;

        $this->load($ids);
        $total_stock = 0;
        foreach ($ids as $id) {
            $total_stock += $this->get($id);
        }

        return (boolean) $total_stock;
    }


    /**
     * Updates the stock status across databases.
     * This really should be moved to an event listener, as it is duplicated in ECommerceServices
     *
     * @param  boolean  $is_out
     * @param  Products $product
     * @return array
     */
    protected function setStockStatus($is_out, Products $product)
    {
        $sql = "
            UPDATE
                products
            SET
                is_out_of_stock = ".(int) $is_out.",
                updated_at = NOW()
            WHERE
                id = ".$product->getId()
        ;

        return $this->replicator->executeQuery($sql, [], $this->warehouse->getRelatedDatabases());
    }
}
