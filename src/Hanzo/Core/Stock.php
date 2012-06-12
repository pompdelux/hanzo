<?php

namespace Hanzo\Core;

use Propel;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsStockPeer;


class Stock
{
    protected $stock = array();
    protected $is_master = 0;

    /**
     * fetch the stock put of db and save it in a static var
     *
     * @param array $products an array of product object
     * @return array();
     */
    protected function load($products)
    {
        if (!is_array($products) && (!$products instanceof \PropelObjectCollection)) {
            $products = array($products);
        }

        $ids = array();
        foreach($products as $product) {
          if (is_object($product)) {
              $id = $product->getId();
          }
          else {
              $id = (int) $product;
          }

          if (isset($this->stock[$id])){
              continue;
          }

          // catch out of stock
          $this->stock[$id] = array();
          $this->stock[$id]['total'] = 0;

          $ids[] = $id;
        }

        if (empty($ids)) {
            return;
        }

        $this->setMasterConnection();
        $result = ProductsStockQuery::create()
            ->orderByAvailableFrom()
            ->filterByProductsId($ids)
            ->find()
        ;
        $this->releaseMasterConnection();

        $now = date('Ymd');

        foreach ($result as $record) {
            $id = $record->getProductsId();
            $date = $record->getAvailableFrom('Ymd');

            $this->stock[$id]['total'] += $record->getQuantity();
            $this->stock[$id][$date] = array(
                'id' => $record->getId(),
                'date' => $record->getAvailableFrom(),
                'quantity' => $record->getQuantity(),
            );
        }
    }


    /**
     * load a collection of products stock to be testet in a loop or the like.
     *
     * @param array $products an array of product objects
     * @return void
     */
    public function prime($products)
    {
        $this->load($products);
    }


    /**
     * check wether or not a product is in stock or not.
     *
     * @param mixed $product a product object or product id
     * @param int $quantity, the quantity to check agianst
     * @return mixed true if the product is available now, a DateTime object if it is available in the future, false if not in stock
     */
    public function check($product, $quantity = 1)
    {
        if (is_object($product)) {
            $id = $product->getId();
        }
        else {
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
                return $date > $now ? new \DateTime($date) : TRUE;
            }
        }

        return FALSE;
    }

    /**
     * get total stock for a product
     *
     * @param mixed $product a product object or product id
     * @return int
     */
    public function get($product, $as_object = false)
    {
        if (is_object($product)) {
            $id = $product->getId();
        }
        else {
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
            return TRUE;
        }

        $stock = $this->get($product, true);
        ksort($stock);
        $total = array_shift($stock);

        // return FALSE if we do not have enough ín stock
        // NICETO: throw exception ?
        if ($total < $quantity) {
            return FALSE;
        }

        // force master connection, and do the rest as a transaction.
        $this->setMasterConnection();
        $con = Propel::getConnection(ProductsStockPeer::DATABASE_NAME);
        $con->beginTransaction();

        try {
            $left = $quantity;
            while ($left > 0) {
                $current = array_shift($stock);

                $item = ProductsStockQuery::create()->findPk($current['id'], $con);
                if ($current['quantity'] <= $left) {
                    $item->delete();
                }
                else {
                    $item->setQuantity($item->getQuantity() - $left);
                    $item->save();
                }

                $left = $left - $current['quantity'];
            }

            if ($total == $quantity){
                $product->setIsOutOfStock(true);
                $product->save($con);
            }

            unset($this->stock[$product->getId()]);
            $con->commit();
        }
        catch(Exception $e) {
            $con->rollback();
            return FALSE;
        }

        $this->releaseMasterConnection();

        return $current['date'];
    }



    /**
     * wrapper function for handeling nested calls to set/get Propel::setForceMasterConnection()
     * we only release master connextions if the call is in the "parent" call
     */
    protected function setMasterConnection()
    {
        if ($this->is_master) {
            $this->is_master++;
            return;
        }

        Propel::setForceMasterConnection(TRUE);
        $this->is_master = 1;
    }

    protected function releaseMasterConnection()
    {
        if ($this->is_master > 1) {
            $this->is_master--;
            return;
        }

        Propel::setForceMasterConnection(FALSE);
        $this->is_master = 0;
    }
}
