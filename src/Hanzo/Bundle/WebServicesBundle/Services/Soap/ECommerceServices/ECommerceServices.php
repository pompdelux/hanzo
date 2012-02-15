<?php

namespace Hanzo\Bundle\WebServicesBundle\Services\Soap\ECommerceServices;

use Hanzo\Bundle\WebServicesBundle\Services\Soap\SoapService;

use Hanzo\Core\Tools;

use Hanzo\Model\ProductsDomainsPricesPeer,
    Hanzo\Model\ProductsDomainsPrices,
    Hanzo\Model\ProductsDomainsPricesQuery,
    Hanzo\Model\ProductsI18nPeer,
    Hanzo\Model\ProductsI18n,
    Hanzo\Model\ProductsI18nQuery,
    Hanzo\Model\ProductsImagesCategoriesSortPeer,
    Hanzo\Model\ProductsImagesCategoriesSort,
    Hanzo\Model\ProductsImagesCategoriesSortQuery,
    Hanzo\Model\ProductsImagesPeer,
    Hanzo\Model\ProductsImages,
    Hanzo\Model\ProductsImagesProductReferencesPeer,
    Hanzo\Model\ProductsImagesProductReferences,
    Hanzo\Model\ProductsImagesProductReferencesQuery,
    Hanzo\Model\ProductsImagesQuery,
    Hanzo\Model\ProductsPeer,
    Hanzo\Model\Products,
    Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsStockPeer,
    Hanzo\Model\ProductsStock,
    Hanzo\Model\ProductsStockQuery,
    Hanzo\Model\ProductsToCategoriesPeer,
    Hanzo\Model\ProductsToCategories,
    Hanzo\Model\ProductsToCategoriesQuery,
    Hanzo\Model\ProductsWashingInstructionsPeer,
    Hanzo\Model\ProductsWashingInstructions,
    Hanzo\Model\ProductsWashingInstructionsQuery,
    Hanzo\Model\DomainsQuery
;

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

        $domains = array();
        foreach(DomainsQuery::create()->find() as $domain) {
            $domains[$domain->getDomainKey()] = $domain->getId();
        }

        // loop over all items
        foreach ($item->InventDim as $entry)
        {
            $sku = trim($item->ItemName . ' ' . $entry->InventColorId . ' ' . $entry->InventSizeId);

            $product = ProductsQuery::create()->findBySku($sku);

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

            // // products i18n
            // $i18n = new ProductsI18n();
            // $i18n->setLocale();

            // products 2 domain
            $collection = new \PropelCollection();
            foreach ($item->WebDomain as $domain) {
                $data = new ProductsDomainsPrices();
                $data->setDomainsId($domains[$domain]);
                $data->setPrice(0.00);
                $data->setVat(0.00);
                $data->setFromDate(time());
                $data->setCurrencyId(0);  // fixme !
                $collection->setData($data);
            }
            $product->setProductsDomainsPricess($collection);

            // products 2 category

            $product->save();
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
$price = $data->priceList;

if (!$price->ItemId) {
$this->logger->addCritical('no ItemId given');
return self::responseStatus('Error', 'SyncPriceListResult', array('no ItemId given'));
}

// ....................
// ..... ze code ......
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
* @return unknown_type
*/
public function SyncInventoryOnHand($data)
{
$errors = array();
$stock = $data->inventoryOnHand->InventSum;

if (!$stock->ItemId) {
$this->logger->addCritical('no ItemId given');
return self::responseStatus('Error', 'SyncPriceListResult', array('no ItemId given'));
}

// ....................
// ..... ze code ......
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
