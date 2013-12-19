<?php

namespace Hanzo\Bundle\StockBundle;

use Hanzo\Core\Tools;
use Propel;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockQuery;
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
            if (is_object($product)) {
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

        foreach ($this->warehouse->getStatus($ids) as $id => $status) {
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

        return FALSE;
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
     * decrease the stock level for a product
     *
     * @NICETO throw execption on error ?
     *
     * @param Products $product a product object
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

        // return FALSE if we do not have enough ín stock
        // NICETO: throw exception ?
        if ($total < $quantity) {
            return false;
        }

        // force master connection, and do the rest as a transaction.
        $con = self::getConnection();
        $con->beginTransaction();

        try {
            $left = $quantity;
            while ($left > 0) {
                $current = array_shift($stock);

                $item = ProductsStockQuery::create()->findPk($current['id'], $con);
                if ($current['quantity'] <= $left) {
                    $item->delete($con);
                }
                else {
                    $item->setQuantity($item->getQuantity() - $left);
                    $item->save($con);
                }

                $left = $left - $current['quantity'];
            }

            if ($total == $quantity){
                $product->setIsOutOfStock(true);
                $product->save($con);

                // if all variants is out of stock, set it on the master product.
                $total_stock = ProductsStockQuery::create()
                    ->withColumn('SUM('.ProductsStockPeer::QUANTITY.')', 'total_stock')
                    ->select(array('total_stock'))
                    ->useProductsQuery()
                        ->filterByMaster($product->getMaster())
                    ->endUse()
                    ->findOne($con)
                ;

                if (0 == $total_stock) {
                    $master = ProductsQuery::create()->findOneBySku($product->getMaster(), $con);

                    $master->setIsOutOfStock(true);
                    $master->save($con);

                    $this->event_dispatcher->dispatch('product.stock.zero', new FilterCategoryEvent($master, $this->locale));
                }
            }

            unset($this->stock[$product->getId()]);
            $con->commit();

        } catch(Exception $e) {
            $con->rollback();
            Tools::log($e->getMessage());
        }

        return $current['date'];
    }

    /**
     * @return \PropelPDO
     */
    protected static function getConnection()
    {
        static $con;

        if (empty($con)) {
            $con = Propel::getConnection(ProductsStockPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        return $con;
    }
}
