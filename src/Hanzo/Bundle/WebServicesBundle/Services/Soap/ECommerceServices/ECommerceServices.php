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

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersLinesQuery;

use Hanzo\Bundle\NewsletterBundle\NewsletterApi;
use Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCall;
use Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException;

use Criteria;
use \Exception;
use \PropelCollection;

class ECommerceServices extends SoapService
{

    /**
     * syncronize an item
     * @param object $data xmlformat:
     *    <item xmlns="http://schemas.pompdelux.dk/webintegration/item">
     *      <InventTable>
     *        <ItemGroupId>G_Access,LG_Access</ItemGroupId>
     *        <ItemGroupName>Girl Accessories</ItemGroupName>
     *        <ItemId>Ada HAT</ItemId>
     *        <WebEnabled>true</WebEnabled>
     *        <ItemName>Ada HAT</ItemName>
     *        <ItemType>Item</ItemType>
     *        <NetWeight>0.00</NetWeight>
     *        <BlockedDate>1990-01-01</BlockedDate>
     *        <InventDim>
     *          <InventColorId>Dark Grey Melange</InventColorId>
     *          <InventSizeId>50</InventSizeId>
     *        </InventDim>
     *        <InventDim>
     *          <InventColorId>Violet</InventColorId>
     *          <InventSizeId>50</InventSizeId>
     *        </InventDim>
     *        <InventDim>
     *          ...
     *        </InventDim>
     *        <WebDomain>COM</WebDomain>
     *        <WebDomain>DK</WebDomain>
     *        <WebDomain>SalesDK</WebDomain>
     *        <Sales>
     *          <Price>0.00</Price>
     *          <UnitId>Stk.</UnitId>
     *        </Sales>
     *        <WashInstruction/>
     *      </InventTable>
     *    </item>
     * @return object SyncItemResult
     */
    public function SyncItem($data)
    {
        require __DIR__.'/products_id_map.php';

        $errors = array();
        $item = $data->item->InventTable;

        if (!$item->ItemGroupId OR !$item->ItemId OR !$item->ItemName) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': no ItemGroupId OR ItemId OR ItemName given');

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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': WebEnabled set to "' . $item->WebEnabled . '", it should be set to "1"', $errors);

            return self::responseStatus('Error', 'SyncItemResult', $errors);
        }

        // domain check
        if (!$item->WebDomain) {
            $errors = array(
                'InventId: ' . $item->ItemId,
                'WebDomain empty, we need at lest one domain to create/update the product.'
            );
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': No WebDomain parameters', $errors);

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
            foreach ($item->InventDim as $entry) {
                /**
                 * create master product
                 */
                if ($index == 0) {
                    $sku = trim($item->ItemName);
                    $product = ProductsQuery::create()->findOneBySku($sku);

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

                    if (!$product instanceof Products) {
                        $product = new Products();
                        $product->setId($products_id_map[strtolower($sku)]);
                        $product->setSku($sku);

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

                    } else {

                        $collection = new PropelCollection();
                        foreach ($categories as $category) {
                            $data = new ProductsToCategories();
                            $data->setCategoriesId($category->getId());
                            $data->setProductsId($product->getId());
                            $collection->prepend($data);
                        }
                        $product->setProductsToCategoriess($collection);
                    }

                    $product->setUnit(trim('1 ' .$item->Sales->UnitId));
                    $washing = $item->WashInstruction;
                    if (is_scalar($washing) && !empty($washing)) {
                        $product->setWashing($washing);
                    }

                    // save ze product
                    $product->save();
                }

                // create product variations
                $sku = trim($item->ItemName . ' ' . $entry->InventColorId . ' ' . $entry->InventSizeId);
                $product = ProductsQuery::create()->findOneBySku($sku);

                if (!$product instanceof Products) {
                    $product = new Products();
                    $product->setId($products_id_map[strtolower($sku)]);
                    $product->setSku($sku);
                    $product->setMaster($item->ItemName);
                    $product->setColor($entry->InventColorId);
                    $product->setSize($entry->InventSizeId);
                }

                $product->setUnit(trim('1 ' .$item->Sales->UnitId));
                $product->setHasVideo(true);
                $product->setIsOutOfStock(false);
                $product->setIsActive(true);

                $washing = $item->WashInstruction;
                if (is_scalar($washing) && !empty($washing)) {
                    $product->setWashing($washing);
                }

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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': SyncItem failed with the following error(s)', $errors);

            return self::responseStatus('Error', 'SyncItemResult', $errors);
        }

        return self::responseStatus('Ok', 'SyncItemResult');
    }

    /**
     * syncronize a products price(s)
     *
     * @param object $data xmlformat:
     *    <priceList xmlns="http://schemas.pompdelux.dk/webintegration/pricelist">
     *      <ItemId>Ada HAT</ItemId>
     *      <SalesPrice>
     *        <AmountCur>20.00</AmountCur>
     *        <Currency>DKK</Currency>
     *        <CustAccount>DKK</CustAccount>
     *        <InventColorId>Dark Grey Melange</InventColorId>
     *        <InventSizeId>50</InventSizeId>
     *        <PriceUnit>1.00</PriceUnit>
     *        <Quantity>1.00</Quantity>
     *        <UnitId>Stk.</UnitId>
     *      </SalesPrice>
     *      <SalesPrice>
     *      ...
     *      </SalesPrice>
     *    </priceList>
     * @return object SyncPriceListResult
     */
    public function SyncPriceList($data)
    {
        $errors = array();
        $prices = $data->priceList;

        if (!$prices->ItemId) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': no ItemId given');
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
        }


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

            $domain = $this->getDomainKeyFromCurrencyKey($entry);

            if (empty($domain)) {
                $errors[] = sprintf("No domain setup for '%s'", $key);
                continue;
            }

            // always have a from date on prices
            if (empty($entry->PriceDate)) {
                $entry->PriceDate = strtotime('-1 day');
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

            $thePrice = $entry->AmountCur;
            $vat = 0;
            if ($domain['vat'] > 0) {
                // 100/(1+(25/100) = 80
                $thePrice = $thePrice / (1 + ($domain['vat'] / 100));
                $vat = $entry->AmountCur - $thePrice;
            }


            // fix decimals in db...
            $vat = number_format( $vat, 2, '.', '' );
            $thePrice = number_format( $thePrice, 2, '.', '' );

            // perhaps we could skip this, pompdelux does not use alternative prices pr. variant
            $products[$key]['prices'][] = array(
                'domain'    => $domain['domain'],
                'currency'  => $domain['currency'],
                'amount'    => $thePrice,
                'vat'       => $vat,
                'from_date' => $entry->PriceDate,
                'to_date'   => (isset($entry->PriceDateTo) ? $entry->PriceDateTo : null),
            );

            // this is here to maintain price info on the master product also
            $products[$prices->ItemId]['prices'][$domain['domain'].$entry->PriceDate] = array(
                'domain'    => $domain['domain'],
                'currency'  => $domain['currency'],
                'amount'    => $thePrice,
                'vat'       => $vat,
                'from_date' => $entry->PriceDate,
                'to_date'   => (isset($entry->PriceDateTo) ? $entry->PriceDateTo : null),
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
                $data->setDomainsId($domains[$price['domain']]);
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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': SyncPriceList failed with the following error(s)', $errors);
            return self::responseStatus('Error', 'SyncPriceListResult', $errors);
        }

        return self::responseStatus('Ok', 'SyncPriceListResult');
    }

    /**
     * inventory syncronization
     *
     * @param object $data xmlformat:
     *    <inventoryOnHand xmlns="http://schemas.pompdelux.dk/webintegration/inventoryOnhand">
     *      <InventSum>
     *        <ItemId>Ada HAT</ItemId>
     *        <InventDim>
     *          <InventColorId>Dark Grey Melange</InventColorId>
     *          <InventSizeId>50</InventSizeId>
     *          <InventQtyAvailOrdered>0.00</InventQtyAvailOrdered>
     *          <InventQtyAvailOrderedDate>2012-08-07</InventQtyAvailOrderedDate>
     *          <InventQtyAvailPhysical>0.00</InventQtyAvailPhysical>
     *          <InventQtyPhysicalOnhand>0.00</InventQtyPhysicalOnhand>
     *        </InventDim>
     *        <InventDim>
     *          ...
     *        </InventDim>
     *     </InventSum>
     *    </inventoryOnHand>
     * @return object
     */
    public function SyncInventoryOnHand($data)
    {
        //Tools::log($data);
        $now = date('Ymd');
        $errors = array();
        $stock = $data->inventoryOnHand->InventSum;

        if (!$stock->ItemId) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': no ItemId given');
            return self::responseStatus('Error', 'SyncPriceListResult', array('no ItemId given'));
        }

        $master = ProductsQuery::create()->findOneBySku($stock->ItemId);
        if (!$master instanceof Products) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': Unknown product, ItemId: ' . $stock->ItemId);
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
                $products[$key]['qty_in_use'] = 0;

                // get any open orders product quantity
                $qty = OrdersLinesQuery::create()
                    ->withColumn('SUM('.OrdersLinesPeer::QUANTITY.')', 'qty')
                    ->filterByProductsId($product->getId())
                    ->groupByProductsId()
                    ->useOrdersQuery()
                        ->filterByState(0, Criteria::LESS_THAN)
                        ->filterByState(Orders::STATE_ERROR_PAYMENT, Criteria::GREATER_THAN)
                        ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), Criteria::LESS_THAN)
                    ->endUse()
                    ->findOne()
                ;

                if ($qty && $qty->getVirtualColumn('qty')) {
                    $products[$key]['qty_in_use'] = $qty->getVirtualColumn('qty');
                }
            }

            $item->InventQtyAvailOrderedDate = $item->InventQtyAvailOrderedDate ? $item->InventQtyAvailOrderedDate : 0;
            $incomming = str_replace('-', '', $item->InventQtyAvailOrderedDate);

            $quantity = $item->InventQtyAvailPhysical;
            if (($incomming > $now) && ($item->InventQtyAvailOrdered > 0)) {
                $quantity = $item->InventQtyAvailOrdered;
            }

            // subtract "reservations"
            if ($products[$key]['qty_in_use']) {
                if ($products[$key]['qty_in_use'] >= $quantity) {
                    $products[$key]['qty_in_use'] = $products[$key]['qty_in_use'] - $quantity;
                    continue;
                }

               $quantity = $quantity -  $products[$key]['qty_in_use'];
               $products[$key]['qty_in_use'] = 0;
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
            $collection = new PropelCollection();

            if (isset($item['inventory'])) {
                // inventory to products
                foreach ($item['inventory'] as $s) {
                    $data = new ProductsStock();
                    $data->setQuantity($s['stock']);
                    $data->setAvailableFrom($s['date']);
                    $collection->prepend($data);
                }
                $product->setProductsStocks($collection);
                $product->setIsOutOfStock(false);
                $allout = false;
            } else {
                $product->setProductsStocks($collection);
                $product->setIsOutOfStock(true);
            }

            $product->save();
        }

        if ($allout) {
            $master->setIsOutOfStock(true);
        } else {
            $master->setIsOutOfStock(false);
        }
        $master->save();

        // ....................
        // .....</ze code>.....
        // ....................

        if (count($errors)) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': SyncInventoryOnHand failed with the following error(s)', $errors);
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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': not a customer object');
            return self::responseStatus('Error', 'SyncCustomerResult', array('not a customer object'));
        }

        $data = $data->customer->CustTable;

        // ....................
        // .....<ze code>......
        // ....................

        $country = CountriesQuery::create()->findOneByIso2($data->AddressCountryRegionId);

        if (!$country instanceof Countries) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': unknown country reference: ' . $data->AddressCountryRegionId . ' for account: #' . $data->AccountNum);
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
                if (!$consultant instanceof Consultants) {
                    $consultant = new Consultants();
                }
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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': SyncCustomerResult failed with the following error(s)', $errors);
            return self::responseStatus('Error', 'SyncCustomerResult', $errors);
        }

        return self::responseStatus('Ok', 'SyncCustomerResult');
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
     * delete a specific sales order.
     *
     * (17:05:32) Enrique: virker det fra ax?
     * (17:05:41) Heinrich: kommer det aldrig til
     * (17:05:46) Enrique: ok?
     * (17:05:53) Enrique: men der er bare en DeleteSalesOrder
     * (17:05:54) Heinrich: det bruger vi ikke
     *
     * @param object $data
     * @return object DeleteSalesOrderResult
     */
    public function DeleteSalesOrder($data)
    {
        if (empty($data->eOrderNumber)) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': no eOrderNumber given.');
            return self::responseStatus('Error', 'DeleteSalesOrderResult', array('no eOrderNumber given.'));
        }

        // ....................
        // .....<ze code>......
        // ....................

        $order = OrdersQuery::create()->findOneById($data->eOrderNumber);

        if (!$order instanceof Orders) {
            $msg = 'no order found with eOrderNumber "' . $data->eOrderNumber . '".';
            $this->logger->addCritical($msg);
            return self::responseStatus('Error', 'DeleteSalesOrderResult', array($msg));
        }

        if ($order->getPaymentGatewayId()) {
            try {
                $order->delete();
            } catch (\Exception $e) {
                $this->logger->addCritical(__METHOD__.' '.__LINE__.': Could not cancel payment: "'.$e->getMessage().'"');
                return self::responseStatus('Error', 'DeleteSalesOrderResult', array('Could not cancel payment: "'.$e->getMessage().'"'));
            }
        }


        // ....................
        // .....</ze code>.....
        // ....................

        if (count($errors)) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': DeleteSalesOrderResult failed with the following error(s)', $errors);
            return self::responseStatus('Error', 'DeleteSalesOrderResult', $errors);
        }

        return self::responseStatus('Ok', 'DeleteSalesOrderResult');
    }


    /**
     * capture or refund an amount on an order
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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': no eOrderNumber given.');
            return self::responseStatus('Error', 'SalesOrderCaptureOrRefundResult', array('no eOrderNumber given.'));
        }

        $order = OrdersQuery::create()->findOneById($data->eOrderNumber);

        if (!$order instanceof Orders) {
            $msg = 'no order found with eOrderNumber "' . $data->eOrderNumber . '".';
            $this->logger->addCritical($msg);
            return self::responseStatus('Error', 'DeleteSalesOrderResult', array($msg));
        }

        // ....................
        // .....<ze code>......
        // ....................

        $this->sendStatusMail = true;
        if ($data->amount && ($data->amount > 0)) {
            // capture
            if ($order->getPaymentGatewayId()) {
                $result = $this->SalesOrderCapture($data, $order);
                if ($result !== true) {
                    $errors = $result;
                }
            }
        } else {
            // refund
            if (($data->amount < 0) && $order->getPaymentGatewayId()) {
                $result = $this->SalesOrderRefund($data, $order);
                if ($result !== true) {
                    $errors = $result;
                }
            }
        }
        // ....................
        // .....</ze code>.....
        // ....................


        if (count($errors)) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': SalesOrderCaptureOrRefundResult failed with the following error(s)', $errors);
            return self::responseStatus('Error', 'SalesOrderCaptureOrRefundResult', $errors);
        }

        $this->SalesOrderLockUnlock((object) array(
            'eOrderNumber' => $data->eOrderNumber,
            'orderStatus' => 4,
            'sendMail' => $this->sendStatusMail,
        ));

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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': no eOrderNumber given.');
            return self::responseStatus('Error', 'SalesOrderLockUnlockResult', array('no eOrderNumber given.'));
        }

        if ($data->orderStatus < 1 || $data->orderStatus > 4) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': order status id #' . $data->orderStatus . ' is not known.');
            return self::responseStatus('Error', 'SalesOrderLockUnlockResult', array('order status id #' . $data->orderStatus . ' is not known.'));
        }

        $order = OrdersQuery::create()->findOneById($data->eOrderNumber);

        if (!$order instanceof Orders) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': order #' . $data->eOrderNumber . ' does not exist.');
            return self::responseStatus('Error', 'SalesOrderLockUnlockResult', array('order #' . $data->eOrderNumber . ' does not exist.'));
        }

        // ....................
        // .....<ze code>......
        // ....................

        $status_map = array(
            1 => Orders::STATE_PENDING,
            2 => Orders::STATE_BUILDING,
            3 => Orders::STATE_BEING_PROCESSED,
            4 => Orders::STATE_SHIPPED,
        );

        if ($data->sendMail) {
            try {
                $name = trim($order->getFirstName() . ' ' . $order->getLastName());
                $mailer = Hanzo::getInstance()->container->get('mail_manager');
                $mailer->setTo($order->getEmail(), $name);
                $mailer->setMessage('order.status_processing', array(
                    'order_id' => $order->getId()
                ));
                $mailer->send();
            } catch (Exception $e) {
                Tools::log($e->getMessage());
            }
        }

        $order->setState($status_map[$data->orderStatus]);

        // the order is considered finished when shipped
        if (4 == $data->orderStatus) {
            $order->setFinishedAt(time());
        }

        $order->save();

        // ....................
        // .....</ze code>.....
        // ....................

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
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': no eOrderNumber given.');
            return self::responseStatus('Error', 'SalesOrderAddDocumentResult', array('no eOrderNumber given.'));
        }

        $order = OrdersQuery::create()->findOneById($data->eOrderNumber);

        if (!$order instanceof Orders) {
            $this->logger->addCritical(__METHOD__.' '.__LINE__.': order #' . $data->eOrderNumber . ' does not exist.');
            return self::responseStatus('Error', 'SalesOrderLockUnlockResult', array('order #' . $data->eOrderNumber . ' does not exist.'));
        }

        // ....................
        // .....<ze code>......
        // ....................

        $attributes = $order->getAttributes();

        $attachment_index = 0;
        if (isset($attributes->attachment)) {
            $attachment_index = count($attributes->attachment);
        }

        $order->setAttribute('attachment_'.$attachment_index, 'attachment', $data->fileName);
        $order->save();

        // ....................
        // .....</ze code>.....
        // ....................

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
     * SalesOrderCapture - split out from SalesOrderCaptureOrRefund
     *
     * @see ECommerceServices::SalesOrderCaptureOrRefund
     * @param stdClass $data  soap data object
     * @param Order    $order order object
     * @return mixed   true on success array of errors on error
     */
    protected function SalesOrderCapture($data, Orders $order)
    {
        $error = array();

        try {
            $tmpAmount = str_replace(',', '.', $data->amount);
            list($large, $small) = explode('.', $tmpAmount);
            $amount = $large . sprintf('%02d', $small);

            $gateway = $this->hanzo->container->get('payment.dibsapi');

            // // TODO: remove when .nl gets its own site
            // // un 2012-08-07, moved to db
            // if (in_array($order->getAttributes()->global->domain_key, array('NL'))) {
            //     $settings = array();
            //     $settings['merchant'] = '90055039';
            //     $settings['md5key1']  = '@6B@(-rfD:DiXYh}(76h6C1rexwZ)-cw';
            //     $settings['md5key2']  = '-|FA8?[K3rb,T$:pJSr^lBsP;hMq&p,X';
            //     $settings['api_user'] = 'pdl-nl-api-user';
            //     $settings['api_pass'] = 'g7u6Ri&c';

            //     $gateway->mergeSettings( $settings );
            // }

            try {
                $response = $gateway->call()->capture($order, $amount);
                $result = $response->debug();
            } catch (DibsApiCallException $e) {
                $error = array(
                    'cound not capture order #' . $data->eOrderNumber,
                    'error: ' . $e->getMessage()
                );
            }

            if ( empty($result['status']) || ($result['status'] != 'ACCEPTED') ) {
                $error = array(
                    'cound not capture order #' . $data->eOrderNumber,
                    'error: ' . $result['status_description']
                );
            }
        } catch (Exception $e) {
            $error = array(
                'cound not capture order #' . $data->eOrderNumber,
                'error: ' . $e->getMessage()
            );
        }

        return count($error) ? $error : true;
    }


    /**
     * SalesOrderRefund - split out from SalesOrderCaptureOrRefund
     *
     * @see ECommerceServices::SalesOrderCaptureOrRefund
     * @param [type] $data  [description]
     * @param Order  $order [description]
     * @return mixed   true on success array of errors on error
     */
    protected function SalesOrderRefund($data, Orders $order)
    {
        $setStatus = false;
        $error = array();

        $amount = str_replace(',', '.', $data->amount);
        list($large, $small) = explode('.', $amount);
        $amount = $large . sprintf('%02d', $small);

        $gateway = $this->hanzo->container->get('payment.dibsapi');

        $doSendError = false;
        try {
            $response = $gateway->call()->refund($order, ($amount * -1));
            $result = $response->debug();

            if ($result['status'] != 0) {
                $doSendError = true;
                $error = array(
                    'cound not refund order #' . $data->eOrderNumber,
                    'error: ' . $result['status_description']
                );
            } else {
                $name = $order->getFirstName() . ' ' . $order->getLastName();
                $parameters = array(
                    'order_id' => $order->getId(),
                    'name' => $name,
                    'amount' => $data->amount,
                );

                $mailer = $this->hanzo->container->get('mail_manager');
                if ($order->getCurrencyCode() == 'EUR') {
                    $mailer->setMessage('order.credited', $parameters, 'en_GB');
                } else {
                    $mailer->setMessage('order.credited', $parameters);
                }

                $mailer->setTo($order->getEmail(), $name);
                $mailer->send();

                $this->sendStatusMail = false;
            }

        } catch (Exception $e) {
            $doSendError = true;
            $error = array(
                'cound not capture order #' . $data->eOrderNumber,
                'error: ' . $e->getMessage()
            );
        }

        if($doSendError) {
            switch (substr($order->getAttributes()->global->domain_name, -2)) {
                case 'dk':
                case 'om':
                    $to = 'retur@pompdelux.dk';
                    break;
                case 'se':
                    $to = 'retur@pompdelux.se';
                    break;
                case 'nl':
                    $to = 'retur@pompdelux.nl';
                    break;
                case 'fi':
                    $to = 'retur@pompdelux.fi';
                    break;
                case 'no':
                    $to = 'retur@pompdelux.no';
                    break;
            }

            $mailer = $this->hanzo->container->get('mail_manager');
            $mailer->setTo($to);
            $mailer->setsubject('Refundering fejlede på ordre #' . $data->eOrderNumber);
            $mailer->setBody("E-ordrenummer: ".$data->eOrderNumber." Transaktionsnummer: ".$order->getPaymentGatewayId()."\n\nMed venlig hilsen DIBS\n");
            $mailer->send();
        }

        return count($error) ? $error : true;
    }



    /**
     * tmp mapping of currency to domain.
     * @fixme: skal laves om i både ax og web. og skal ikke være hardcoded
     */
    public function getDomainKeyFromCurrencyKey($entry)
    {
        $k = strtolower($entry->Currency.'.'.$entry->CustAccount);

        $c_map = array(
            'dkk.dkk'     => array('currency' => 'DKK', 'domain' => 'DK', 'vat' => 25),
            'eur.eur'     => array('currency' => 'EUR', 'domain' => 'COM', 'vat' => 25),
            'dkk.salesdk' => array('currency' => 'DKK', 'domain' => 'SalesDK', 'vat' => 25),

            'nok.nok'     => array('currency' => 'NOK', 'domain' => 'NO', 'vat' => 25),
            'nok.salesno' => array('currency' => 'NOK', 'domain' => 'SalesNO', 'vat' => 25),

            'sek.sek'     => array('currency' => 'NOK', 'domain' => 'SE', 'vat' => 25),
            'sek.salesse' => array('currency' => 'NOK', 'domain' => 'SalesSE', 'vat' => 25),

            'eur.fin'     => array('currency' => 'NOK', 'domain' => 'FI', 'vat' => 23),
            'eur.salesfi' => array('currency' => 'NOK', 'domain' => 'SalesFI', 'vat' => 23),

            'eur.nld'     => array('currency' => 'NOK', 'domain' => 'NL', 'vat' => 19),
            'eur.salesnl' => array('currency' => 'NOK', 'domain' => 'SalesNL', 'vat' => 19),
        );

        return isset($c_map[$k]) ? $c_map[$k] : false;
    }
}
