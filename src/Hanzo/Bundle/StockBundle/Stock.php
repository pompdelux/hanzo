<?php

namespace Hanzo\Bundle\StockBundle;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockPeer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Hanzo\Bundle\AdminBundle\Event\FilterCategoryEvent;

class Stock
{
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
     * @param $locale
     * @param EventDispatcher $event_dispatcher
     * @param Warehouse       $warehouse
     */
    public function __construct($locale, EventDispatcher $event_dispatcher, Warehouse $warehouse)
    {
        $this->event_dispatcher = $event_dispatcher;
        $warehouse->setLocation($locale);
        $this->warehouse = $warehouse;
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
            if ($date === 'total') {
                continue;
            }

            $sum += $stock['quantity'];
            if ($stock['quantity'] >= $quantity) {
                return $date > $now ? new \DateTime($date) : true;
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
     * @param int $quantity the quantity by wich to decrease
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
            $con = self::getConnection();

            $product->setIsOutOfStock(true);
            $product->save($con);
            $this->warehouse->removeProductFromInventory($product_id);

            // find out if the whole style is out of stock
            // if so, tag it so and fire an event (for caching n' stuff)
            if (false === $this->checkStyleStock($product)) {
                $master = ProductsQuery::create()->findOneBySku($product->getMaster(), $con);
                $master->setIsOutOfStock(true);
                $master->save($con);

                $this->event_dispatcher->dispatch('product.stock.zero', new FilterCategoryEvent($master, $this->locale));
            }
        }

        unset($this->stock[$product->getId()]);

        return $current['date'];
    }


    /**
     * Figure out whether or not a whole style is out of stock.
     *
     * @param $product
     * @return bool
     */
    protected function checkStyleStock($product)
    {
        $ids = ProductsQuery::create()
            ->select('Id')
            ->filterByMaster($product->getMaster())
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
     * @return \PropelPDO
     */
    protected static function getConnection()
    {
        static $con;

        if (empty($con)) {
            $con = \Propel::getConnection(ProductsStockPeer::DATABASE_NAME, \Propel::CONNECTION_WRITE);
        }

        return $con;
    }
}
