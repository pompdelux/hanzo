<?php

namespace Hanzo\Model;

use \PropelPDO;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\om\BaseOrders,
    Hanzo\Model\OrdersLines,
    Hanzo\Model\OrdersLinesPeer,
    Hanzo\Model\OrdersLinesQuery
;


/**
 * Skeleton subclass for representing a row from the 'orders' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class Orders extends BaseOrders
{
  /**
   * Definition of the different status a order can have
   *
   */
  const STATE_PRE_CONFIRM     = -30;
  const STATE_PRE_PAYMENT     = -20;
  const STATE_POST_PAYMENT    = 10;
  const STATE_PAYMENT_OK      = 20;
  const STATE_PENDING         = 30;
  const STATE_BEING_PROCESSED = 40;
  const STATE_SHIPPED         = 50;
  const STATE_ERROR           = 100;
  const STATE_ERROR_PAYMENT   = 110;

  /**
   * set quantity on a product line in the current order
   *
   * @param Product $product
   * @param int $quantity can be positive to increase the quantity of the order or negative to decrease
   * @param bool $exact if set to true, the quantity send is the quantity used, otherwise the quantity is calculated using the existing as offset.
   * @return OrdersLines
   */
  public function setOrderLineQty($product, $quantity, $exact = FALSE)
  {
    // first update existing product lines, if any
    $lines = $this->getOrdersLiness();
    foreach ($lines as $index => $line) {
      if ($product->getId() == $line->getProductsId()) {
        $offset = 0;
        if (FALSE === $exact) {
          $offset = $line->getQuantity();
        }

        $line->setQuantity($offset + $quantity);
        $lines[$index] = $line;
        $this->setOrdersLiness($lines);

        return;
      }
    }

    // if the product is not already on the order, add it.

    // fetch price information
    $price = ProductsDomainsPricesPeer::getProductsPrices(array($product->getId()));
    $price = array_shift($price);
    $price = array_shift($price);

    $line = new OrdersLines;
    $line->setOrdersId($this->getId());
    $line->setProductsId($product->getId());
    $line->setProductsName($product->getMaster());
    $line->setProductsSku($product->getSku());
    $line->setQuantity($quantity);
    $line->setPrice($price['price']);
    $line->setTax($price['vat']);
    $line->setType('product');
    $this->addOrdersLines($line);
  }


  public function preSave(PropelPDO $con = null)
  {
    if (!$this->getSessionId()){
      $this->setSessionId(session_id());
    }

    return true;
  }

  public function postSave(PropelPDO $con = null)
  {
    if(empty($_SESSION['order_id'])) {
      $_SESSION['order_id'] = $this->getId();
    }
  }


  public function getTotalPrice()
  {
    $lines = $this->getOrdersLiness();

    $total = 0;
    foreach ($lines as $line) {
      $total += ($line->getPrice() * $line->getQuantity());
    }

    return $total;
  }

  public function getTotalQuantity()
  {
    $lines = $this->getOrdersLiness();

    $total = 0;
    foreach ($lines as $line) {
      $total += $line->getQuantity();
    }

    return $total;
  }

} // Orders
