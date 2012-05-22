<?php

namespace Hanzo\Bundle\WebServicesBundle\Services\Soap\ECommerceServices;

use Hanzo\Bundle\WebServicesBundle\Services\Soap\SoapService;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStock;
use Hanzo\Model\ProductsToCategories;

use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Consultants;
use Hanzo\Model\ConsultantsQuery;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesQuery;

use Hanzo\Model\DomainsQuery;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\LanguagesQuery;

use Hanzo\Bundle\NewsletterBundle\NewsletterApi;

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
                $product->setHasVideo(true);
                $product->setIsOutOfStock(false);
                $product->setIsActive(true);

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

        if (empty($prices->SalesPrice)) {
            Tools::log($prices);
            die();
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
        $products = array(
            "{$prices->ItemId}" => array(
                'product' => ProductsQuery::create()->findOneBySku($prices->ItemId),
                'prices' => array()
            )
        );

        foreach ($prices->SalesPrice as $entry)
        {
            $key = $prices->ItemId . ' ' . $entry->InventColorId . ' ' . $entry->InventSizeId;
            $domain_key = self::getDomainKeyFromCurrencyKey($entry->CustAccount);

            if (empty($domain_key)) {
                $errors[] = sprintf("No domain setup for '%s'", $key);
                continue;
            }

            // always have a from date on prices
            if (empty($entry->PriceDate)) {
                $entry->PriceDate = time();
            }


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

            // perhaps we could skip this, pompdelux does not use alternative prices pr. variant
            $products[$key]['prices'][] = array(
                'domain' => $domain_key,
                'currency' => $currencies[$entry->Currency],
                'amount' => $thePrice,
                'vat' => $vat,
                'from_date' => $entry->PriceDate,
                'to_date' => (isset($entry->PriceDateTo) ? $entry->PriceDateTo : null),
            );

            // this is here to maintain price info on the master product also
            $products[$prices->ItemId]['prices'][$domain_key.$entry->PriceDate] = array(
                'domain' => $domain_key,
                'currency' => $currencies[$entry->Currency],
                'amount' => $thePrice,
                'vat' => $vat,
                'from_date' => $entry->PriceDate,
                'to_date' => (isset($entry->PriceDateTo) ? $entry->PriceDateTo : null),
            );
        }

        foreach ($products as $item) {
            $product = $item['product'];
            $prices = $item['prices'];

            if (!$product instanceof Products) {
                continue;
            }

            // products 2 domain
            $collection = new PropelCollection();
            foreach ($prices as $price) {
                $data = new ProductsDomainsPrices();
                $data->setDomainsId($price['domain']);
                $data->setPrice($price['amount']);
                $data->setVat($price['vat']);
                $data->setFromDate($price['from_date']);
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

        $master = ProductsQuery::create()->findOneBySku($stock->ItemId);
        if (!$master instanceof Products) {
            $this->logger->addCritical('Unknown product');
            return self::responseStatus('Error', 'SyncPriceListResult', array('Unknown ItemId: ' . $stock->ItemId));
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

            if (empty($products[$key]['inventory'])) {
                $products[$key]['inventory'] = array();
            }

            $products[$key]['inventory'][] = array(
                'date' => $item->InventQtyAvailOrderedDate,
                'stock' => $quantity
            );
        }

        $allout = true;
        foreach ($products as $item) {
            $product = $item['product'];

            if (isset($item['inventory'])) {
                // inventory to products
                $collection = new PropelCollection();
                foreach ($item['inventory'] as $s) {
                    $data = new ProductsStock();
                    $data->setQuantity($s['stock']);
                    $data->setAvailableFrom($s['date']);
                    $collection->prepend($data);
                }
                $product->setProductsStocks($collection);
                $allout = false;
            }
            else {
                $product->setIsOutOfStock(true);
            }

            $product->save();
        }

        if ($allout) {
            $master->setIsOutOfStock(true);
            $master->save();
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
     *     <AddressCountryRegionId>DK</AddressCountryRegionId>
     *     <CustCurrencyCode>DKK</CustCurrencyCode>
     *     <Email>mariedelice@hotmail.com</Email>
     *     <Phone>004632060004</Phone>
     *     <PhoneLocal></PhoneLocal>
     *     <PhoneMobile></PhoneMobile>
     *     <TeleFax></TeleFax>
     *     <SalesDiscountPercent></SalesDiscountPercent>
     *   </CustTable>
     * </customer>
     */
    public function SyncCustomer($data)
    {
        /**
         * customer groups is as following:
         *  id: >= 10,001 < 15,000 = consultants = 2
         *  id: >= 15.000 < 20,000 = employees   = 3
         *  id: >= 20,000          = customers   = 1
         */

        $errors = array();

        if (!$data instanceof \stdClass || empty($data->customer->CustTable)) {
            $this->logger->addCritical('not a customer object');
            return self::responseStatus('Error', 'SyncCustomerResult', array('not a customer object'));
        }

        $data = $data->customer->CustTable;

        // ....................
        // .....<ze code>......
        // ....................

        $country = CountriesQuery::create()->findOneByIso2($data->AddressCountryRegionId);

        if (!$country instanceof Countries) {
            $this->logger->addCritical('unknown country reference: ' . $data->AddressCountryRegionId . ' for account: #' . $data->AccountNum);
            return self::responseStatus('Error', 'SyncCustomerResult', array('unknown country reference: ' . $data->AddressCountryRegionId . ' for account: #' . $data->AccountNum));
        }

        $group_id = ($data->AccountNum < 15000 ? 2 : ($data->AccountNum < 20000 ? 3 : 1));

        $customer = CustomersQuery::create()->findOneById($data->AccountNum);
        if (!$customer instanceof Customers) {
            $customer = new Customers();
            $customer->setId($data->AccountNum);
            // we never update passwords
            $customer->setPassword(sha1($data->Phone));
            $customer->setPasswordClear($data->Phone);
        }

        if ($group_id > 1) {
            if ($customer->isNew()) {
                $consultant = new Consultants();
            } else {
                $consultant = $customer->getConsultants();
            }
            $consultant->setInitials($data->InitialsId);
            $customer->setConsultants($consultant);
        }

        $names = explode(' ', $data->CustName);
        $first_name = array_shift($names);
        $last_names = implode(' ', $names);

        $customer->setFirstName($first_name);
        $customer->setLastName($last_names);

        $customer->setEmail($data->Email);
        $customer->setPhone($data->Phone);

        if ($data->SalesDiscountPercent) {
            $customer->setDiscount((float) $data->SalesDiscountPercent * -1);
        }

        // create or update primary (payment) address
        if ($customer->isNew()) {
            $address = new Addresses();
            $address->setType('payment');
        } else {
            $address = AddressesQuery::create()
                ->filterByType('payment')
                ->findOneByCustomersId($customer->getId())
            ;
        }

        $address->setFirstName($first_name);
        $address->setLastName($last_names);
        $address->setAddressLine1($data->AddressStreet);
        $address->setPostalCode($data->AddressZipCode);
        $address->setCity($data->AddressCity);
        $address->setCountry($country->getName());
        $address->setCountriesId($country->getId());
        $address->geocode();

        if ($customer->isNew()) {
            $customer->addAddresses($address);
        } else {
            $address->save();
        }

        try {
            $customer->save();

            // user created, add him/her to phplist
            if ($group_id > 1) {
                $hanzo = Hanzo::getInstance();
                $domain_key = $hanzo->get('core.domain_key');
                $newsletter_lists = $hanzo->get('phplist.lists');
                if (isset($newsletter_lists[$domain_key])) {
                    $newsletter = new NewsletterApi();
                    $result = $newsletter->subscribe($customer->getEmail(), $newsletter_lists[$domain_key]);
                }
            }

        } catch (\Exception $e) {
            $errors[] = 'Cound not create or update account.';
            $errors[] = $e->getMessage();
        }


        // ....................
        // .....</ze code>.....
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
// .....<ze code>......
// ....................

// ....................
// .....</ze code>.....
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
// .....<ze code>......
// ....................

// ....................
// .....</ze code>.....
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
// .....<ze code>......
// ....................

// ....................
// .....</ze code>.....
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

// ....................
// .....<ze code>......
// ....................

// ....................
// .....</ze code>.....
// ....................

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



    /**
     * tmp mapping of currency to domain.
     * @fixme: skal laves om i både ax og web.
     *
     * @param  [type] $currencyKey [description]
     * @return [type]              [description]
     */
    protected static function getDomainKeyFromCurrencyKey($currencyKey)
    {
        $domainKey = '';
        switch (strtoupper($currencyKey)) {
            case 'DKK':
                $domainKey = 'DK';
                break;
            case 'SEK':
                $domainKey = 'SE';
                break;
            case 'NOK':
                $domainKey = 'NO';
                break;
            case 'NLD':
                $domainKey = 'NL';
                break;
            case 'EUR':
                $domainKey = 'COM';
                break;
            case 'ØVRIG':
                $domainKey = 'COM';
                break;

            case 'SALES':
            case 'SALESDK':
                $domainKey = 'SalesDK';
                break;
            case 'SALESSE':
                $domainKey = 'SalesSE';
                break;
            case 'SALESNO':
                $domainKey = 'SalesNO';
                break;
        }

        return $domainKey;
    }
}
