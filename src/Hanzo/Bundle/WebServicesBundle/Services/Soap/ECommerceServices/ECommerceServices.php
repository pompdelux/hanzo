<?php

namespace Hanzo\Bundle\WebServicesBundle\Services\Soap\ECommerceServices;

use Hanzo\Bundle\WebServicesBundle\Services\Soap\SoapService;

use Hanzo\Core\Tools;

use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesPeer;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesProductReferencesPeer;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockPeer;
use Hanzo\Model\ProductsStock;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsToCategoriesPeer;
use Hanzo\Model\ProductsToCategories;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsWashingInstructionsPeer;
use Hanzo\Model\ProductsWashingInstructions;
use Hanzo\Model\ProductsWashingInstructionsQuery;

use Hanzo\Model\DomainsQuery;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\LanguagesQuery;

use \Exception;
use \PropelCollection;

class ECommerceServices extends SoapService
{

    /**
     * syncronize an item
     * @param object $data xmlformat:
     * <item>
     *   <InventTable>
     *     <ItemGroupId>G_S-S</ItemGroupId>
     *     <ItemGroupName>Girls Shorts-Skirts</ItemGroupName>
     *     <ItemId>Daisy SKIRT</ItemId>
     *     <WebEnabled nil="true"/>
     *     <ItemName>Daisy SKIRT</ItemName>
     *     <ItemType>Vare</ItemType>
     *     <NetWeight>0</NetWeight>
     *     <BlockedDate>1970-01-01+01:00</BlockedDate>
     *     <InventDim>
     *       <InventSizeId>5-6</InventSizeId>
     *       <InventColorId>Rød</InventColorId>
     *     </InventDim>
     *     <InventDim>
     *       ...
     *     </InventDim>
     *     <Sales>
     *       <Price>150</Price>
     *       <PriceUnit>1</PriceUnit>
     *       <StandardQty>10</StandardQty>
     *       <UnitId>Stk</UnitId>
     *     </Sales>
     *   </InventTable>
     * </item>
     *
     * @return object SyncItemResult
     */
    public function SyncItem($data)
    {
        $errors = array();
        $item = $data->item->InventTable;

        if (!$item->ItemGroupId OR !$item->ItemId OR !$item->ItemName) {
            $this->logger->addCritical('no ItemGroupId OR ItemId OR ItemName given');

            return self::responseStatus('Error', 'SyncItemResult', array(
                'no ItemGroupId OR ItemId OR ItemName given'
            ));
        }

        // only allow web enabled ptoducts to go into the database.
        if ($item->WebEnabled != 1) {
            $errors = array(
                'InventId: ' . $item->ItemId,
                'WebEnabled set to "' . $item->WebEnabled . '" it should be set to "1"'
            );
            $this->logger->addCritical('WebEnabled set to "' . $item->WebEnabled . '", it should be set to "1"', $errors);

            return self::responseStatus('Error', 'SyncItemResult', $errors);
        }

        // domain check
        if (!$item->WebDomain) {
            $errors = array(
                'InventId: ' . $item->ItemId,
                'WebDomain empty, we need at lest one domain to create/update the product.'
            );
            $this->logger->addCritical('No WebDomain parameters', $errors);

            return self::responseStatus('Error', 'SyncItemResult', $errors);
        }

        if (!is_array($item->WebDomain)) {
            $item->WebDomain = array($item->WebDomain);
        }

        if (!is_array($item->InventDim)) {
            $item->InventDim = array($item->InventDim);
        }


        // ....................
        // .....<ze code>......
        // ....................

        try {
            // loop over all items
            $index = 0;
            foreach ($item->InventDim as $entry)
            {
                /**
                 * create master product
                 */
                if ($index == 0) {
                    $sku = trim($item->ItemName);
                    $product = ProductsQuery::create()->findOneBySku($sku);

                    if (!$product instanceof Products) {
                        $product = new Products();
                        $product->setSku($sku);
                        $product->setUnit(trim('1 ' .$item->Sales->UnitId));
                        $product->setWashing(trim($item->WashInstruction));

                        // products 2 category
                        $categories = explode(',', $item->ItemGroupId);

                        // cleanup elements
                        array_walk($categories, function(&$value, $key) use (&$categories) {
                            $value = trim($value);
                            if (empty($value)) {
                                unset ($categories[$key]);
                            }
                        });

                        $categories = CategoriesQuery::create()
                            ->filterByContext($categories)
                            ->find();

                        if (0 == $categories->count()) {
                            $errors = array(
                                'InventId: ' . $item->ItemId,
                                'No category/categories found for ItemGroupId: '.$item->ItemGroupId.'.',
                            );
                            break;
                        }

                        // products i18n
                        $languages = LanguagesQuery::create()->find();
                        foreach ($languages as $language) {
                            $i18n = new ProductsI18n();
                            $i18n->setLocale($language->getLocale());
                            $i18n->setTitle($item->ItemName);
                            $i18n->setContent($item->ItemName);
                            $product->addProductsI18n($i18n);
                        }

                        // bind product to categories
                        foreach ($categories as $category) {
                            $p2c = new ProductsToCategories();
                            $p2c->setCategoriesId($category->getId());
                            $product->addProductsToCategories($p2c);
                        }

                        // save ze product
                        $product->save();
                    }
                }

                // create product variations
                $sku = trim($item->ItemName . ' ' . $entry->InventColorId . ' ' . $entry->InventSizeId);
                $product = ProductsQuery::create()->findOneBySku($sku);

                if (!$product instanceof Products) {
                    $product = new Products();
                    $product->setSku($sku);
                    $product->setMaster($item->ItemName);
                    $product->setColor($entry->InventColorId);
                    $product->setSize($entry->InventSizeId);
                }

                $product->setUnit(trim('1 ' .$item->Sales->UnitId));
                $product->setWashing(trim($item->WashInstruction));
                $product->setHasVideo(TRUE);
                $product->setIsOutOfStock(FALSE);
                $product->setIsActive(TRUE);

                // moved to SyncPriceList
                // // products 2 domain
                // $collection = new PropelCollection();
                // foreach ($item->WebDomain as $domain) {
                //     $data = new ProductsDomainsPrices();
                //     $data->setDomainsId($domains[$domain]);
                //     $data->setPrice(0.00);
                //     $data->setVat(0.00);
                //     $data->setFromDate(time());
                //     $data->setCurrencyId(0);  // fixme !
                //     $collection->setData($data);
                // }
                // $product->setProductsDomainsPricess($collection);

                $product->save();
                $index++;
            }

        }
        catch(Exception $e) {
            $errors = array(
                //'InventId: ' . $item->ItemId,
                $e->getMessage(),
            );
        }


        // ....................
        // .....</ze code>.....
        // ....................

        if (count($errors)) {
            $this->logger->addCritical('SyncItem failed with the following error(s)', $errors);

            return self::responseStatus('Error', 'SyncItemResult', $errors);
        }

        return self::responseStatus('Ok', 'SyncItemResult');
    }

    /**
     * syncronize a products price(s)
     *
     * @param object $data xmlformat:
     * <priceList>
     *   <ItemId>Daisy SKIRT</ItemId>
     *   <SalesPrice>
     *     <AmountCur>127.5</AmountCur>
     *     <Currency>DKK</Currency>
     *     <CustAccount/>
     *     <InventColorId>Rød</InventColorId>
     *     <InventSizeId>5-6</InventSizeId>
     *     <PriceDate>2008-12-08</PriceDate>
     *     <PriceDateTo>2009-12-08</PriceDateTo>
     *     <PriceUnit>1</PriceUnit>
     *     <Quantity>1</Quantity>
     *     <UnitId>Stk</UnitId>
     *   </SalesPrice>
     *   <SalesPrice>
     *     ...
     *   </SalesPrice>
     * </priceList>
     *
     * @return object SyncPriceListResult
     */
    public function SyncPriceList($data)
    {
        $errors = array();
        $prices = $data->priceList;

        if (!$prices->ItemId) {
            $this->logger->addCritical('no ItemId given');
            return self::responseStatus('Error', 'SyncPriceListResult', array('no ItemId given'));
        }

        if (!is_array($prices->SalesPrice)) {
            $prices->SalesPrice = array($prices->SalesPrice);
        }

        // ....................
        // .....<ze code>......
        // ....................

        $domains = array();
        foreach(DomainsQuery::create()->find() as $domain) {
            $domains[$domain->getDomainKey()] = $domain->getId();
            $domains['Sales'.$domain->getDomainKey()] = $domain->getId();
        }

        // FIXME:
        $currencies = array(
            'DKK' => 1,
            'NOK' => 2,
            'SEK' => 3,
            'EUR' => 4,
            'EUR' => 5,
        );


        $error = array();
        $products = array();

        foreach ($prices->SalesPrice as $entry)
        {
            $key = $prices->ItemId . ' ' . $entry->InventColorId . ' ' . $entry->InventSizeId;

            if (empty($domains[$entry->CustAccount])) {
                $errors[] = sprintf("No domain setup for '%s'", $key);
                continue;
            }
            $domain_key = $domains[$entry->CustAccount];

            if (empty($products[$key])) {
                $product = ProductsQuery::create()
                    ->filterByMaster($prices->ItemId)
                    ->filterByColor($entry->InventColorId)
                    ->filterBySize($entry->InventSizeId)
                    ->findOne()
                ;

                // catch unknown products
                if (!$product instanceof Products) {
                    $errors[] = sprintf("'%s' does not exist in products", $key);
                    continue;
                }

                $products[$key]['product'] = $product;
            }

            switch ($entry->CustAccount)
            {
                case 'ØVRIG':
                    $thePrice = $entry->AmountCur;
                    $vat = 0;
                    break;
                default:
                    $thePrice = $entry->AmountCur * 0.8;
                    $vat = $entry->AmountCur - $thePrice;
                    break;
            }

            $products[$key]['prices'][] = array(
                'domain' => $domain_key,
                'currency' => $currencies[$entry->Currency],
                'amount' => $thePrice,
                'vat' => $vat,
                'from_date' => $entry->PriceDate,
                'to_date' => $entry->PriceDateTo,
            );
        }

        foreach ($products as $item) {
            $product = $item['product'];
            $prices = $item['prices'];

            // products 2 domain
            $collection = new PropelCollection();
            foreach ($prices as $price) {
                $data = new ProductsDomainsPrices();
                $data->setDomainsId($price['domain']);
                $data->setPrice($price['amount']);
                $data->setVat($price['vat']);
                $data->setFromDate($price['from_date']);
                // only add to_date if set.
                if ($price['to_date']) {
                    $data->setToDate($price['to_date']);
                }
                $data->setCurrencyId($price['currency']);
                $collection->prepend($data);
            }

            $product->setProductsDomainsPricess($collection);
            $product->save();
        }

        // ....................
        // .....</ze code>.....
        // ....................


        if (count($errors)) {
            $this->logger->addCritical('SyncPriceList failed with the following error(s)', $errors);
            return self::responseStatus('Error', 'SyncPriceListResult', $errors);
        }

        return self::responseStatus('Ok', 'SyncPriceListResult');
    }

    /**
     * inventory syncronization
     *
     * @param object $data xmlformat:
     * <inventoryOnHand>
     *   <InventSum>
     *     <ItemId>Daisy SKIRT</ItemId>
     *     <InventDim>
     *       <InventColorId>Rød</InventColorId>
     *       <InventSizeId>5-6</InventSizeId>
     *       <InventQtyAvailOrdered>0</InventQtyAvailOrdered>
     *       <InventQtyAvailOrderedDate></InventQtyAvailOrderedDate>
     *       <InventQtyAvailPhysical>0</InventQtyAvailPhysical>
     *       <InventQtyPhysicalOnhand>0</InventQtyPhysicalOnhand> <!-- never use(d) -->
     *     </InventDim>
     *     <InventDim>
     *       ...
     *     </InventDim>
     *   </InventSum>
     * </inventoryOnHand>
     * @return object
     */
    public function SyncInventoryOnHand($data)
    {
        //Tools::log($data);
        $now = date('Ymd');
        $errors = array();
        $stock = $data->inventoryOnHand->InventSum;

        if (!$stock->ItemId) {
            $this->logger->addCritical('no ItemId given');
            return self::responseStatus('Error', 'SyncPriceListResult', array('no ItemId given'));
        }

        // ....................
        // .....<ze code>......
        // ....................

        // if there is only one item it is not send as an array
        if (!is_array($stock->InventDim))
        {
            $stock->InventDim = array($stock->InventDim);
        }

        $products = array();
        foreach($stock->InventDim as $item) {
            $key = $stock->ItemId . ' ' . $item->InventColorId . ' ' . $item->InventSizeId;

            if (empty($products[$key])) {
                $product = ProductsQuery::create()
                    ->filterByMaster($stock->ItemId)
                    ->filterByColor($item->InventColorId)
                    ->filterBySize($item->InventSizeId)
                    ->findOne()
                ;

                if (!$product instanceof Products) {
                    $errors[] = "{$key} not a known product";
                    continue;
                }

                $products[$key]['product'] = $product;
            }

            // alm lager:
            // [InventColorId] => Navy
            // [InventSizeId] => 80
            // [InventQtyAvailOrdered] => 0.00
            // [InventQtyAvailOrderedDate] => 2012-02-28
            // [InventQtyAvailPhysical] => 63.00
            // [InventQtyPhysicalOnhand] => 79.00
            //
            // bestilt lager:
            // [InventColorId] => Navy
            // [InventSizeId] => 110-116
            // [InventQtyAvailOrdered] => 23.00
            // [InventQtyAvailOrderedDate] => 2012-03-25
            // [InventQtyAvailPhysical] => 0.00
            // [InventQtyPhysicalOnhand] => 0.00


            $item->InventQtyAvailOrderedDate = $item->InventQtyAvailOrderedDate ? $item->InventQtyAvailOrderedDate : 0;
            $incomming = str_replace('-', '', $item->InventQtyAvailOrderedDate);

            $quantity = $item->InventQtyAvailPhysical;
            if (($incomming > $now) && ($item->InventQtyAvailOrdered > 0)) {
                $quantity = $item->InventQtyAvailOrdered;
            }

            // no need to add empty entries
            if ($quantity == 0) {
                continue;
            }

            $products[$key]['inventory'][] = array(
                'date' => $item->InventQtyAvailOrderedDate,
                'stock' => $quantity
            );
        }

        foreach ($products as $item) {
            $product = $item['product'];
            $inventory = $item['inventory'];

            // inventory 2 products
            $collection = new PropelCollection();
            foreach ($inventory as $s) {
                $data = new ProductsStock();
                $data->setQuantity($s['stock']);
                $data->setAvailableFrom($s['date']);
                $collection->prepend($data);
            }

            $product->setProductsStocks($collection);
            $product->save();
        }

        // ....................
        // .....</ze code>.....
        // ....................

        if (count($errors)) {
            $this->logger->addCritical('SyncInventoryOnHand failed with the following error(s)', $errors);
            return self::responseStatus('Error', 'SyncInventoryOnHandResult', $errors);
        }

        return self::responseStatus('Ok', 'SyncInventoryOnHandResult');
    }


/**
* customer syncronizer
*
* @param object $data xmlformat:
* <customer>
*   <CustTable>
*     <AccountNum>109381</AccountNum>
*     <InitialsId></InitialsId>
*     <CustName>Mamarie Karlsson</CustName>
*     <AddressStreet>marknadsvøgen 7</AddressStreet>
*     <AddressCity>bjærketorp</AddressCity>
*     <AddressZipCode>51994</AddressZipCode>
*     <AddressCountryRegionId>DKK</AddressCountryRegionId>
*     <CustCurrencyCode>DKK</CustCurrencyCode>
*     <Email>mariedelice@hotmail.com</Email>
*     <Phone>004632060004</Phone>
*     <PhoneLocal></PhoneLocal>
*     <PhoneMobile></PhoneMobile>
*     <TeleFax></TeleFax>
*   </CustTable>
* </customer>
*/
public function SyncCustomer($data)
{
$errors = array();

if (!$data instanceof stdClass || empty($data->customer->CustTable)) {
parent::log('error', 'not a customer object');
return self::responseStatus('Error', 'SyncCustomerResult', array('not a customer object'));
}

// ....................
// ..... ze code ......
// ....................

if (count($errors)) {
$this->logger->addCritical('SyncCustomerResult failed with the following error(s)', $errors);
return self::responseStatus('Error', 'SyncCustomerResult', $errors);
}

return self::responseStatus('Ok', 'SyncCustomerResult');
}

/**
* delete a specific sales order.
*
* @param object $data
* @return object DeleteSalesOrderResult
*/
public function DeleteSalesOrder($data)
{
if (empty($data->eOrderNumber)) {
$this->logger->addCritical('no eOrderNumber given.');
return self::responseStatus('Error', 'DeleteSalesOrderResult', array('no eOrderNumber given.'));
}

//$order = bc_getOrderById($data->eOrderNumber);

if (empty($order)) {
$errors[] = 'no order found with eOrderNumber "' . $data->eOrderNumber . '".';
}

// ....................
// ..... ze code ......
// ....................

if (count($errors)) {
$this->logger->addCritical('DeleteSalesOrderResult failed with the following error(s)', $errors);
return self::responseStatus('Error', 'DeleteSalesOrderResult', $errors);
}

return self::responseStatus('Ok', 'DeleteSalesOrderResult');
}


/**
* set status to invoiced on a specific order.
*
* @todo
* @param object $data
* @return object SyncSalesOrderResult
*/
public function SyncSalesOrder($data){}


/**
* capture or refund an amout on an order
* on success we update the order status to 4, and sends an email
*
* @see ECommerceServices::SalesOrderLockUnlock
* @param stdClass $data
*   $data->eOrderNumber; // string
*   $data->amount;       // decimal
*   $data->initials;     // string
* @return object SalesOrderCaptureOrRefundResult
*/
public function SalesOrderCaptureOrRefund ($data)
{
$errors = array();

if (empty($data->eOrderNumber)) {
$this->logger->addCritical('no eOrderNumber given.');
return self::responseStatus('Error', 'SalesOrderCaptureOrRefundResult', array('no eOrderNumber given.'));
}

//$order = bc_getOrderById($data->eOrderNumber);

if (empty($order)) {
$errors[] = 'order #' . $data->eOrderNumber . ' does not exist.';
}
else {

// ....................
// ..... ze code ......
// ....................

}

if (count($errors)) {
$this->logger->addCritical('SalesOrderCaptureOrRefundResult failed with the following error(s)', $errors);
return self::responseStatus('Error', 'SalesOrderCaptureOrRefundResult', $errors);
}

return self::responseStatus('Ok', 'SalesOrderCaptureOrRefundResult');
}


/**
* change status of an order, the method also sends statuschanges on request.
*
* @param stdCLass $data
*   $data->eOrderNumber; // string
*   $data->orderStatus;  // integer
*   $data->sendMail;     // bool
* @return object SalesOrderLockUnlockResult
*/
public function SalesOrderLockUnlock ($data)
{
$errors = array();

if (empty($data->eOrderNumber)) {
$this->logger->addCritical('no eOrderNumber given.');
return self::responseStatus('Error', 'SalesOrderLockUnlockResult', array('no eOrderNumber given.'));
}

if ($data->orderStatus < 1 || $data->orderStatus > 4) {
$this->logger->addCritical('order status id #' . $data->orderStatus . ' is not known.');
return self::responseStatus('Error', 'SalesOrderLockUnlockResult', array('order status id #' . $data->orderStatus . ' is not known.'));
}

// $order = bc_getOrderById($data->eOrderNumber);

if (empty($order)) {
$this->logger->addCritical('order #' . $data->eOrderNumber . ' does not exist.');
return self::responseStatus('Error', 'SalesOrderLockUnlockResult', array('order #' . $data->eOrderNumber . ' does not exist.'));
}

// ....................
// ..... ze code ......
// ....................


if (count($errors)) {
$this->logger->addCritical('SalesOrderLockUnlockResult failed with the following error(s)', $errors);
return self::responseStatus('Error', 'SalesOrderLockUnlockResult', $errors);
}

return self::responseStatus('Ok', 'SalesOrderLockUnlockResult');
}


/**
* Attach a document to a sales order.
*
* @param object $data
* @return object SalesOrderAddDocumentResult
*/
public function SalesOrderAddDocument($data)
{
$errors = array();

if (empty($data->eOrderNumber)) {
$this->logger->addCritical('no eOrderNumber given.');
return self::responseStatus('Error', 'SalesOrderAddDocumentResult', array('no eOrderNumber given.'));
}

if (count($errors)) {
$this->logger->addCritical('SalesOrderAddDocumentResult failed with the following error(s)', $errors);
return self::responseStatus('Error', 'SalesOrderAddDocumentResult', $errors);
}

return self::responseStatus('Ok', 'SalesOrderAddDocumentResult');
}


/**
* building responce message
*
* @param string $status
* @param string $var message type
* @param array $messages array of messages to send
*
* @return object
*/
protected function responseStatus ($status, $var, $messages = array())
{
$response = new \stdClass();
$response->{$var} = new \stdClass();
$response->{$var}->Status = new \SoapVar($status, \XSD_STRING, "", "http://schemas.pompdelux.dk/webintegration/ResponseStatus");

foreach ($messages as $message) {
$response->{$var}->Message[] = new \SoapVar($message, \XSD_STRING, "", "http://schemas.pompdelux.dk/webintegration/ResponseStatus");
}

return $response;
}
}
