<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use Exception;
use BasePeer;
use Criteria;
use PropelPDO;
use PropelCollection;
use PropelException;
use OutOfBoundsException;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\om\BaseOrders;

/**
 * Class Orders
 *
 * @package Hanzo\Model
 */
class Orders extends BaseOrders
{
    /**
     * Definition of the different status a order can have
     */
    const STATE_ERROR_PAYMENT   = -110;
    const STATE_ERROR           = -100;
    const STATE_BUILDING        =  -50;
    const STATE_PRE_CONFIRM     =  -30; // nuke ???
    const STATE_PRE_PAYMENT     =  -20;
    const STATE_PAYMENT_OK      =   20;
    const STATE_PENDING         =   30; // sidste "edit" step
    const STATE_BEING_PROCESSED =   40; // hos ax
    const STATE_SHIPPED         =   50; // lukket

    const TYPE_PRIVATE          =  -1;
    const TYPE_GIFT             =  -2;
    const TYPE_FRIEND           =  -3;
    const TYPE_OUTSIDE_EVENT    =  -4;
    const TYPE_NORMAL           = -10;

    public static $state_message_map = [
        self::STATE_ERROR_PAYMENT   => 'Payment error',
        self::STATE_ERROR           => 'General error',
        self::STATE_BUILDING        => 'Building order',
        self::STATE_PRE_CONFIRM     => 'Order in pre confirm state',
        self::STATE_PRE_PAYMENT     => 'Order in pre payment state',
        self::STATE_PAYMENT_OK      => 'Order payment confirmed',
        self::STATE_PENDING         => 'Order pending',
        self::STATE_BEING_PROCESSED => 'Order beeing processed',
        self::STATE_SHIPPED         => 'Order shipped/done',
    ];

    /**
     * Used purely for info in the state log, and is here only for reference.
     */
    const INFO_STATE_IN_QUEUE                = 'Order in AX transfer queue';
    const INFO_STATE_EDIT_STARTED            = 'Edit started';
    const INFO_STATE_EDIT_CANCLED_BY_USER    = 'Edit cancled by user';
    const INFO_STATE_EDIT_CANCLED_BY_CLEANUP = 'Edit cancled by cleanup';
    const INFO_STATE_EDIT_DONE               = 'Edit done';

    protected $ignoreDeleteConstraints = false;

    /**
     * Unmapped payment id, used in ax sync
     *
     * @var int
     */
    private $paymentTransactionId;

    /**
     * Unmapped endpoint, used in ax sync
     *
     * @var string
     */
    private $endPoint;

    /**
     * @var \PDO|\PropelPDO
     */
    protected $dbConn = null;

    /**
     * @var bool
     */
    protected $skipPreEventMetaData = false;

    /**
     * Needed by recreatedDeletedOrdersAction to prevent the event
     * handeler from setting metadata already handled by the function.
     */
    public function setSkipPreEventMetaData()
    {
        $this->skipPreEventMetaData = true;
    }

    /**
     * Check the state of $skipPreEventMetaData
     *
     * @see OnPreSaveEvent::handle
     * @return bool
     */
    public function getSkipPreEventMetaData()
    {
        return $this->skipPreEventMetaData;
    }


    /**
     * Get DB Connection
     *
     * @return null|\PDO|\PropelPDO
     */
    public function getDBConnection()
    {
        return $this->dbConn;
    }

    /**
     * Set DB Connection
     *
     * @param \PDO|\PropelPDO $connection
     */
    public function setDBConnection($connection)
    {
        $this->dbConn = $connection;
    }

    /**
     * Set payment transaction id, note this is used in ax "delete order" actions
     *
     * @param int $id
     */
    public function setPaymentTransactionId($id)
    {
        $this->paymentTransactionId = $id;
    }

    /**
     * Get payment transaction id, note this is used in ax "delete order" actions
     *
     * @return int
     */
    public function getPaymentTransactionId()
    {
        return $this->paymentTransactionId;
    }

    /**
     * @param string $v
     */
    public function setEndPoint($v)
    {
        $this->endPoint = $v;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * @param Translator $translator
     *
     * @return string
     */
    public function getDeliveryTitle(Translator $translator = null)
    {
        $title = parent::getDeliveryTitle();
        if ('' == $title) {
            return '';
        }

        return $this->translateNameTitle($translator, $title);
    }

    /**
     * @param Translator $translator
     *
     * @return string
     */
    public function getDeliveryFullName(Translator $translator = null)
    {
        return trim($this->getDeliveryTitle($translator).' '.$this->getDeliveryFirstName().' '.$this->getDeliveryLastName());
    }

    /**
     * @param Translator $translator
     *
     * @return string
     */
    public function getBillingTitle(Translator $translator = null)
    {
        return $this->translateNameTitle($translator, parent::getBillingTitle());
    }

    /**
     * @param Translator $translator
     * @param string     $title
     *
     * @return string
     */
    private function translateNameTitle($translator, $title)
    {
        if ($title && ($translator instanceof Translator)) {
            $title = $translator->trans('title.'.$title, [], 'account');
        }

        return $title;
    }

    /**
     * Get full customer name.
     *
     * @return string
     */
    public function getCustomersName()
    {
        return trim($this->getFirstName().' '.$this->getLastName());
    }

    /**
     * Create a new version of the current order.
     *
     * @return object Orders
     */
    public function createNewVersion()
    {
        // never create a new version of a new object.
        if ($this->isNew()) {
            return $this;
        }

        /**
         * we need to version the following tables:
         *     orders
         *     orders_products
         *     orders_attributes
         */
        $data = [];
        $data['order'] = $this->toArray();
        unset($data['order']['Id']);

        $data['products'] = $this->getOrdersLiness($this->getDBConnection())->toArray();
        $data['attributes'] = $this->getOrdersAttributess($this->getDBConnection())->toArray();

        $versionIds = $this->getVersionIds();

        if (count($versionIds)) {
            $versionId = max($versionIds) +1;
        } else {
            $versionId = 1;
        }

        $version = new OrdersVersions();
        $version->setOrdersId($this->getId());
        $version->setVersionId($versionId);
        $version->setContent(serialize($data));
        $version->setCreatedAt(time());
        $version->save();

        $this->setVersionId($versionId + 1);
        $this->save();

        return $this;
    }

    /**
     * Get all version ids including the current version
     *
     * @return array
     */
    public function getVersionIds()
    {
        $versions = OrdersVersionsQuery::create()
            ->select('VersionId')
            ->filterByOrdersId($this->getId())
            ->orderByVersionId('desc')
            ->find($this->getDBConnection());

        $ids = [];
        foreach ($versions as $version) {
            $ids[$version] = $version;
        }

        return $ids;
    }

    /**
     * delete a version, note you cannot delete the current version
     *
     * @param int $versionId the version you which to delete
     */
    public function deleteVersion($versionId)
    {
        if (!in_array($versionId, $this->getVersionIds())) {
            throw new OutOfBoundsException('Invalid version id');
        }

        OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->findOneByVersionId($versionId)
            ->delete();
    }

    /**
     * switch the order object to another version
     *
     * @param int $versionId the version id to switch to
     *
     * @return object     Orders
     * @throws \Exception
     */
    public function toVersion($versionId)
    {
        // if it's the same version, just return self.
        if ($this->getVersionId() == $versionId) {
            return $this;
        }

        $version = OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByVersionId($versionId)
            ->findOne($this->getDBConnection());

        if (!$version instanceof OrdersVersions) {
            throw new OutOfBoundsException('No such version: ' . $versionId . ' of order nr: ' . $this->getId());
        }

        $data = unserialize($version->getContent());

        // start by setting the order.
        $this->fromArray($data['order']);

        // set product lines
        $collection = new PropelCollection();
        foreach ($data['products'] as $item) {
            unset($item['Id']);
            $line = new OrdersLines();
            $line->fromArray($item);
            $collection->prepend($line);
        }
        $this->setOrdersLiness($collection);

        OrdersAttributesQuery::create()
            ->findByOrdersId($this->getId(), $this->getDBConnection())
            ->delete();

        $this->clearOrdersAttributess();

        $collection = new PropelCollection();
        foreach ($data['attributes'] as $item) {
            $line = new OrdersAttributes();
            $line->fromArray($item);
            $collection->prepend($line);
        }
        $this->setOrdersAttributess($collection);

        // save and return the version
        try {
            $this->save();
        } catch (PropelException $e) {
            Tools::log($e->getMessage()."\n\n".print_r($this->toArray(), 1), 0, true);
            throw $e;
        }

        return $this;
    }

    /**
     * getOrderAtVersion
     * Based on this->toVersion
     *
     * @param int $versionId
     *
     * @return Orders
     * @throws OutOfBoundsException
     */
    public function getOrderAtVersion($versionId)
    {
        // if it's the same version, just return self.
        if ($this->getVersionId() == $versionId) {
            return $this;
        }

        $version = OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByVersionId($versionId)
            ->findOne($this->getDBConnection());

        if (!$version instanceof OrdersVersions) {
            throw new OutOfBoundsException('No such version: ' . $versionId . ' of order nr: ' . $this->getId());
        }

        $data = unserialize($version->getContent());

        $order = new Orders();
        // start by setting the order.
        $order->fromArray($data['order']);

        // set product lines
        $collection = new PropelCollection();
        foreach ($data['products'] as $item) {
            unset($item['Id']);
            $line = new OrdersLines();
            $line->fromArray($item);
            $collection->prepend($line);
        }

        $order->setOrdersLiness($collection);

        foreach ($data['attributes'] as $item) {
            $line = new OrdersAttributes();
            $line->fromArray($item);
            $order->addOrdersAttributes($line);
        }

        // save and return the version
        return $order;
    }

    /**
     * Go one version back
     *
     * @return object Orders
     */
    public function toPreviousVersion()
    {
        // no previous version, return current
        if (count($this->getVersionIds()) < 1) {
            return $this;
        }

        $version = OrdersVersionsQuery::create()
            ->select('VersionId')
            ->filterByOrdersId($this->getId())
            ->filterByVersionId($this->getVersionId(), \Criteria::LESS_THAN)
            ->orderByVersionId('desc')
            ->findOne($this->getDBConnection());

        $this->toVersion($version);

        // delete abandoned version
        $this->deleteVersion($version);

        return $this;
    }

    /**
     * set quantity on a product line in the current order
     *
     * @param Products $product  the product
     * @param int      $quantity can be positive to increase the quantity of the order or negative to decrease
     * @param bool     $exact    if set to true, the quantity send is the quantity used, otherwise the quantity is calculated using the existing as offset.
     * @param string   $date     availability date
     *
     * @return OrdersLines
     */
    public function setOrderLineQty($product, $quantity, $exact = false, $date = '1970-01-01')
    {
        // first update existing product lines, if any
        $lines = $this->getOrdersLiness(null, $this->getDBConnection());

        if ($this->getState() !== self::STATE_BUILDING) {
            $this->setState(self::STATE_BUILDING);
        }

        foreach ($lines as $index => $line) {
            if ($product->getId() == $line->getProductsId()) {
                $offset = 0;
                if (false === $exact) {
                    $offset = $line->getQuantity();
                }

                $line->setQuantity($offset + $quantity);
                $lines[$index] = $line;
                $this->setOrdersLiness($lines);
                $line->setExpectedAt($date);

                return;
            }
        }

        // if the product is not already on the order, add it.

        // fetch price information
        $price = ProductsDomainsPricesPeer::getProductsPrices([$product->getId()]);

        $price         = array_shift($price);
        $originalPrice = $price['normal'];
        $price         = array_shift($price);

        $line = new OrdersLines();
        $line->setOrdersId($this->getId());
        $line->setProductsId($product->getId());
        $line->setProductsName($product->getTitle());
        $line->setProductsSku($product->getSku());
        $line->setProductsColor($product->getColor());
        $line->setProductsSize($product->getSize());
        $line->setQuantity($quantity);
        $line->setPrice($price['price']);
        $line->setOriginalPrice($originalPrice['price']);
        $line->setVat($price['vat']);
        $line->setType('product');
        $line->setUnit('Stk.');
        $line->setExpectedAt($date);

        if ($product->getIsVoucher()) {
            $line->setIsVoucher(true);
        }

        $this->addOrdersLines($line);
    }

    /**
     * setOrderLineShipping
     *
     * NICETO: rewrite to use self::setOrderLine
     *
     * Does not set products_id as the external id might clash with a real product + it may contain letters
     *
     * @param ShippingMethods $shippingMethod
     * @param bool            $isFee
     *
     * @return void
     */
    public function setShipping(ShippingMethods $shippingMethod, $isFee = false)
    {
        $sku = $shippingMethod->getFeeExternalId();
        $name = $shippingMethod->getName();

        if ($isFee) {
            $price = $shippingMethod->getFee();
            $type  = 'shipping.fee';
        } else {
            $price = $shippingMethod->getPrice();
            $type  = 'shipping';
        }

        $line = OrdersLinesQuery::create()
            ->filterByType($type)
            ->filterByOrdersId($this->getId())
            ->findOne($this->getDBConnection());

        if (!$line instanceof OrdersLines) {
            $line = new OrdersLines();
            $line->setOrdersId($this->getId());
            $line->setType($type);
            $line->setQuantity(1);
            $line->setVat(0.00);
        }

        $line->setProductsSku($sku);
        $line->setProductsName($name);
        $line->setPrice($price);
        $line->setVat(0.00);
        $line->save();
    }

    /**
     * NICETO: create filter function that is used by getOrderLineXXX
     *
     * @param null|PropelPDO $conn
     *
     * @return array|mixed
     */
    public function getOrderLineShipping($conn = null)
    {
        $conn = is_null($conn)
            ? $this->getDBConnection()
            : $conn;

        return OrdersLinesQuery::create()
            ->filterByType(['shipping', 'shipping.fee'], Criteria::IN)
            ->filterByOrdersId($this->getId())
            ->find($conn);
    }

    /**
     * getOrderLineDiscount
     * NICETO: create filter function that is used by getOrderLineXXX
     *
     * @param null|PropelPDO $conn
     *
     * @return array|mixed
     */
    public function getOrderLineDiscount($conn = null)
    {
        $conn = is_null($conn)
            ? $this->getDBConnection()
            : $conn;

        return OrdersLinesQuery::create()
            ->filterByType('discount')
            ->filterByOrdersId($this->getId())
            ->find($conn);
    }

    /**
     * set or update a discount line
     *
     * @param string $name     discount identifier
     * @param float  $amount   discount amount
     * @param string $discount line discount in percent
     *
     * @return Orders
     */
    public function setDiscountLine($name, $amount, $discount = '')
    {
        foreach ($this->getOrderLineDiscount() as $line) {
            if ($name == $line->getProductsSku()) {
                $line->setPrice(number_format($amount, 4, '.', ''));

                return $this;
            }
        }

        $line = new OrdersLines();
        $line->setType('discount');
        $line->setQuantity(1);
        $line->setVat(0.00);
        $line->setOrdersId($this->getId());
        $line->setProductsSku($name);
        $line->setProductsName($discount);
        $line->setPrice(number_format($amount, 4, '.', ''));
        $this->addOrdersLines($line);

        return $this;
    }

    /**
     * remove a discount line from an order
     *
     * @param string $name discount identifier
     *
     * @return object Orders
     */
    public function removeDiscountLine($name)
    {
        foreach ($this->getOrderLineDiscount() as $line) {
            if ($name == $line->getProductsSku()) {
                $line->delete();
                break;
            }
        }

        return $this;
    }

    /**
     * getTotalProductPrice
     *
     * @return float
     */
    public function getTotalProductPrice()
    {
        return $this->getTotalPrice(true);
    }

    /**
     * @param bool $productsOnly
     *
     * @return int|string
     */
    public function getTotalPrice($productsOnly = false)
    {
        // this is done so Orders::getOrderAtVersion don't throw up
        if ($this->isNew()) {
            $lines = $this->getOrdersLiness();
        } else {
            $query = OrdersLinesQuery::create()->filterByOrdersId($this->getId());

            if ($productsOnly) {
                $query->filterByType('product');
            }

            $lines = $query->find($this->getDBConnection());
        }

        $total = 0;
        foreach ($lines as $line) {
            $total += ($line->getPrice() * $line->getQuantity());
        }

        return $total;
    }

    /**
     * @return int|string
     */
    public function getTotalVat()
    {
        $lines = $this->getOrdersLiness();

        $total = 0;
        foreach ($lines as $line) {
            $total += ($line->getVat() * $line->getQuantity());
        }

        return $total;
    }

    /**
     * @param bool $productsOnly
     *
     * @return int
     */
    public function getTotalQuantity($productsOnly = false)
    {
        if ($this->isNew()) {
            $lines = $this->getOrdersLiness();
        } else {
            $query = OrdersLinesQuery::create()->filterByOrdersId($this->getId());

            if ($productsOnly) {
                $query->filterByType('product');
            }

            $lines = $query->find($this->getDBConnection());
        }

        $total = 0;
        foreach ($lines as $line) {
            $total += $line->getQuantity();
        }

        return $total;
    }

    /**
     * Sets an order attribute
     *
     * @param string $key   Name of the attribute
     * @param string $ns    Namespace of the attribute, e.g. payment
     * @param string $value The value of the attribute
     *
     * @return object Orders object returned to keep the chain alive.
     */
    public function setAttribute($key, $ns, $value)
    {
        $attributes = $this->getOrdersAttributess(null, $this->getDBConnection());

        // Update existing attributes
        foreach ($attributes as $index => $attribute) {
            if (($attribute->getCKey() == $key) &&
                ($attribute->getNs() == $ns)
            ) {
                $attribute->setCValue($value);

                return $this;
            }
        }

        $attribute = new OrdersAttributes();
        $attribute->setCKey($key);
        $attribute->setNs($ns);
        $attribute->setCValue($value);

        $this->addOrdersAttributes($attribute);

        return $this;
    }

    /**
     * setPaymentMethod
     *
     * @param string $method
     *
     * @return void
     */
    public function setPaymentMethod($method)
    {
        $this->setBillingMethod($method);
    }

    /**
     * setPaymentPaytype
     *
     * @param string $paytype
     *
     * @return void
     */
    public function setPaymentPaytype($paytype)
    {
        $this->setAttribute('paytype', 'payment', $paytype);
    }

    /**
     * getPaymentPaytype
     * @return string Payment type
     **/
    public function getPaymentPaytype()
    {
        $attributes = $this->getAttributes();

        if (isset($attributes->payment->paytype)) {
            return $attributes->payment->paytype;
        }

        return false;
    }

    /**
     * setOrderLinePaymentFee
     *
     * Note, this only supports one line with payment fee
     *
     * NICETO: rewrite to use self::setOrderLine
     * @param string $name
     * @param float  $price
     * @param float  $vat
     * @param string $sku
     *
     * @see setOrderLineShipping
     *
     * @return void
     */
    public function setPaymentFee($name, $price, $vat, $sku)
    {
        $fee = OrdersLinesQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByType('payment.fee')
            ->findOne($this->getDBConnection());

        if (!$fee instanceof OrdersLines) {
            $fee = new OrdersLines();
            $fee->setOrdersId($this->getId());
            $fee->setQuantity(1);
            $fee->setType('payment.fee');
        }

        $fee->setProductsName($name);
        $fee->setProductsSku($sku);
        $fee->setPrice($price);
        $fee->setVat($vat);
        $fee->save();
    }

    /**
     * getPaymentFee
     *
     * Note: only supports one payment.fee line
     *
     * @return float
     */
    public function getPaymentFee()
    {
        $line = OrdersLinesQuery::create()
            ->filterByType('payment.fee')
            ->filterByOrdersId($this->getId())
            ->findOne($this->getDBConnection());

        if ($line instanceof OrdersLines) {
            return $line->getPrice();
        }

        return 0.00;
    }

    /**
     * getShippingFee
     *
     * Note: only supports one shipping.fee line
     *
     * @return float
     */
    public function getShippingFee()
    {
        $line = OrdersLinesQuery::create()
            ->filterByType('shipping.fee')
            ->filterByOrdersId($this->getId())
            ->findOne($this->getDBConnection());

        if ($line instanceof OrdersLines) {
            return $line->getPrice();
        }

        return 0.00;
    }

    /**
     * set an orderline
     * note if the type is not "product" only one line pr. type is handled
     *
     * @param string $type     the line type
     * @param int    $id       product id, must be set even for virtual lines
     * @param string $name     line description
     * @param float  $price    price
     * @param float  $vat      vat
     * @param int    $quantity quantity
     *
     * @return object Orders object returned to keep the chain alive.
     */
    public function setOrderLine($type, $id, $name, $price = 0.00, $vat = 0.00, $quantity = 1)
    {
        $lines = $this->getOrdersLiness(null, $this->getDBConnection());

        foreach ($lines as $index => $line) {
            if ($line->getType() == $type) {
                if ($type != 'product') {
                    $line->setProductsId($id);
                    $line->setProductsName($name);
                    $line->setPrice($price);
                    $line->setVat($vat);
                    $line->setQuantity($quantity);
                    $lines[$index] = $line;
                    $this->setOrdersLiness($lines);

                    // maintain chain, return self
                    return $this;
                } else {
                    if ($line->getProductsId() == $id) {
                        $line->setProductsName($name);
                        $line->setPrice($price);
                        $line->setVat($vat);
                        $line->setQuantity($quantity);
                        $lines[$index] = $line;
                        $this->setOrdersLiness($lines);

                        // maintain chain, return self
                        return $this;
                    }
                }
            }
        }

        // add new line
        $line = new OrdersLines();
        $line->setType($type);
        $line->setOrdersId($this->getId());
        $line->setProductsId($id);
        $line->setProductsName($name);
        $line->setQuantity($quantity);
        $line->setPrice($price);
        $line->setVat($vat);
        $this->addOrdersLines($line);

        // maintain chain, return self
        return $this;
    }

    /**
     * setBillingAddress
     *
     * @param Addresses $address
     *
     * @return void
     * @throws Exception
     */
    public function setBillingAddress(Addresses $address)
    {
        if ( $address->getType() != 'payment' ) {
            throw new Exception('Address is not of type payment');
        }

        $this->setBillingAddressLine1($address->getAddressLine1())
            ->setBillingAddressLine2($address->getAddressLine2())
            ->setBillingCity($address->getCity())
            ->setBillingPostalCode($address->getPostalCode())
            ->setBillingCountry($address->getCountry())
            ->setBillingCountriesId($address->getCountriesId())
            ->setBillingStateProvince($address->getStateProvince())
            ->setBillingCompanyName($address->getCompanyName())
            ->setBillingTitle($address->getTitle())
            ->setBillingFirstName($address->getFirstName())
            ->setBillingLastName($address->getLastName())
            ->setBillingExternalAddressId($address->getExternalAddressId());
    }

    /**
     * clearBillingAddress
     */
    public function clearBillingAddress()
    {
        $this->fromArray([
            'BillingTitle'             => null,
            'BillingAddressLine1'      => null,
            'BillingAddressLine2'      => null,
            'BillingCity'              => null,
            'BillingPostalCode'        => null,
            'BillingCountry'           => null,
            'BillingCountriesId'       => null,
            'BillingStateProvince'     => null,
            'BillingCompanyName'       => null,
            'BillingFirstName'         => null,
            'BillingLastName'          => null,
            'BillingExternalAddressId' => null,
        ]);
    }

    /**
     * clearBillingAddress
     */
    public function clearDeliveryAddress()
    {
        $this->fromArray([
            'DeliveryTitle'             => null,
            'DeliveryAddressLine1'      => null,
            'DeliveryAddressLine2'      => null,
            'DeliveryCity'              => null,
            'DeliveryPostalCode'        => null,
            'DeliveryCountry'           => null,
            'DeliveryCountriesId'       => null,
            'DeliveryStateProvince'     => null,
            'DeliveryCompanyName'       => null,
            'DeliveryFirstName'         => null,
            'DeliveryLastName'          => null,
            'DeliveryExternalAddressId' => null,
        ]);
    }

    /**
     * clearPaymentAttributes
     *
     * @return void
     */
    public function clearPaymentAttributes()
    {
        $this->clearAttributesByNS('payment');
    }

    /**
     * clearFees
     *
     * @return int
     */
    public function clearFees()
    {
        return OrdersLinesQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByType('payment.fee')
            ->delete($this->getDBConnection());
    }

    /**
     * clearAttributesByKey
     *
     * @param string $key
     *
     * @return int
     */
    public function clearAttributesByKey($key)
    {
        return OrdersAttributesQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByCKey($key)
            ->delete();
    }

    /**
     * clearAttributesByNS
     *
     * @param string $ns
     *
     * @return int
     */
    public function clearAttributesByNS($ns)
    {
        return OrdersAttributesQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByNs($ns)
            ->delete();
    }

    /**
     * setDeliveryAddress
     *
     * @param Addresses $address
     *
     * @return void
     * @throws Exception
     */
    public function setDeliveryAddress(Addresses $address)
    {
        if (!in_array($address->getType(), ['shipping','overnightbox', 'company_shipping'])) {
            throw new Exception('Delivery address is not a valid type "'.$address->getType().'"');
        }

        $this->setDeliveryAddressLine1($address->getAddressLine1())
            ->setDeliveryAddressLine2($address->getAddressLine2())
            ->setDeliveryCity($address->getCity())
            ->setDeliveryPostalCode($address->getPostalCode())
            ->setDeliveryCountry($address->getCountry())
            ->setDeliveryCountriesId($address->getCountriesId())
            ->setDeliveryStateProvince($address->getStateProvince())
            ->setDeliveryCompanyName($address->getCompanyName())
            ->setDeliveryTitle($address->getTitle())
            ->setDeliveryFirstName($address->getFirstName())
            ->setDeliveryLastName($address->getLastName())
            ->setDeliveryExternalAddressId($address->getExternalAddressId());
    }

    /**
     * Fetch an array of attached documents.
     *
     * @return array
     */
    public function getAttachments()
    {
        $attributes = OrdersAttributesQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByNs('attachment')
            ->find($this->getDBConnection());

        $attachments = [];
        foreach ($attributes as $attribute) {
            $attachments[$attribute->getCKey()] = $attribute->getCValue();
        }

        return $attachments;
    }

    /**
     * @param null $con
     *
     * @return \stdClass
     */
    public function getAttributes($con = null)
    {
        if ($this->getDBConnection()) {
            $con = $this->getDBConnection();
        }

        $attributes = new \stdClass();
        foreach ($this->getOrdersAttributess(null, $con) as $attr) {
            $ns = str_replace([':', '.'], '_', $attr->getNs());

            if (empty($attributes->{$ns})) {
                $attributes->{$ns} = new \stdClass();
            }
            $attributes->{$ns}->{$attr->getCKey()} = $attr->getCValue();
        }

        return $attributes;
    }

    /**
     * Wrapping the setPaymentGatewayId method to auto-generate gateway id's
     *
     * @param int $gatewayId if specified, this is used over the auto generated one
     *
     * @return Orders The current object (for fluent API support)
     */
    public function setPaymentGatewayId($gatewayId = null)
    {
        return parent::setPaymentGatewayId($gatewayId);
    }

    /**
     * Wrapping the setState method to log all state changes
     *
     * @param int $v state id
     *
     * @return Orders The current object (for fluent API support)
     */
    public function setState($v)
    {
        // no need to set the state to its own state..
        if ($v == $this->getState()) {
            return $this;
        }

        $log = new OrdersStateLog();
        $log->setOrdersId($this->getId());
        $log->setState($v);
        $log->setMessage(self::$state_message_map[$v]);
        $log->setCreatedAt(time());

        $this->addOrdersStateLog($log);

        return parent::setState($v);
    }

    /**
     * Check wether a product is in the "cart" or not
     *
     * @param mixed $productId id or sku of the product
     *
     * @return bool
     */
    public function hasProduct($productId)
    {
        $isInt = preg_match('/^[0-9]+$/', $productId);
        foreach ($this->getOrdersLiness(null, $this->getDBConnection()) as $line) {
            if ($isInt) {
                if ($line->getProductsId() == $productId) {
                    return true;
                }
            } else {
                // note the "name" here is the same as the master "sku"
                if ($line->getProductsName() == $productId) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * returns latest delivery date.
     *
     * @param string $format
     * @param string $domainKey
     *
     * @throws Exception
     * @throws PropelException
     * @return mixed|string
     */
    public function getExpectedDeliveryDate($format = 'Y-m-d', $domainKey = '')
    {
        $now        = date('Ymd');
        $latest     = 0;
        $expectedAt = '';

        if (empty($domainKey)) {
            $result     = Hanzo::getInstance()->get('HD.expected_delivery_date');
            $expectedAt = $result ?: '';
        } else {
            $setting = DomainsSettingsQuery::create()
                ->filterByDomainKey($domainKey)
                ->filterByNs('HD')
                ->filterByCKey('expected_delivery_date')
                ->findOne($this->getDBConnection());

            if ($setting && $setting->getCValue()) {
                $expectedAt = $setting->getCValue();
            }
        }

        foreach ($this->getOrdersLiness(null, $this->getDBConnection()) as $line) {
            $date = $line->getExpectedAt('Ymd');
            if (($date > $now) && ($date > $latest)) {
                $latest = $date;
                $expectedAt = $line->getExpectedAt($format);
            }
        }

        return $expectedAt;
    }

    /**
     * @param bool $v
     */
    public function setIgnoreDeleteConstraints($v)
    {
        $this->ignoreDeleteConstraints = (bool) $v;
    }

    /**
     * @return bool
     */
    public function getIgnoreDeleteConstraints()
    {
        return $this->ignoreDeleteConstraints;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function recalculate()
    {
        $hanzo = Hanzo::getInstance();

        if ('' == $this->getBillingFirstName()) {
            $customer = $this->getCustomers();
            if ($customer instanceof Customers) {
                $c = new Criteria();
                $c->add(AddressesPeer::TYPE, 'payment');
                $this->setBillingAddress($customer->getAddressess($c)->getFirst());
            }
        }

        if ('COM' == $hanzo->get('core.domain_key')) {
            $country = $this->getCountriesRelatedByBillingCountriesId();
            if ($country && $country->getVat()) {
                return;
            }

            $lines = $this->getOrdersLiness();

            $productIds = [];
            foreach ($lines as $line) {
                if ('product' == $line->getType()) {
                    $productIds[] = $line->getProductsId();
                }
            }

            $prices = ProductsDomainsPricesPeer::getProductsPrices($productIds);
            $collection = new PropelCollection();

            foreach ($lines as $line) {
                if ('product' == $line->getType()) {
                    $price = $prices[$line->getProductsId()];

                    $sales = $price['normal'];
                    if (isset($price['sales'])) {
                        $sales = $price['sales'];
                    }

                    $line->setPrice($sales['price']);
                    $line->setVat(0);
                    $line->setOriginalPrice($price['normal']['price']);
                }

                $collection->prepend($line);
            }

            $this->setOrdersLiness($collection);
        }

        return $this;
    }

    /**
     * figure out if the order is for a hostess or not
     *
     * @return boolean
     */
    public function isHostessOrder()
    {
        $attributes = $this->getAttributes();
        if (isset($attributes->event->is_hostess_order)) {
            return true;
        }

        return false;
    }

    /**
     * build and return a order Addresses object based on the type
     *
     * @param string $type Can be either of the types set in the addresses table
     *
     * @return Addresses
     */
    public function getOrderAddress($type = 'payment')
    {
        $part = 'billing_';
        if ('payment' != $type) {
            $type = $this->getDeliveryMethod();
            $part = 'delivery_';
        }

        $address = [
            'customers_id' => $this->getCustomersId(),
            'type' => $type,
        ];

        foreach ($this->toArray(\BasePeer::TYPE_FIELDNAME) as $key => $value) {
            $key = str_replace($part, '', $key, $count);
            if ($count) {
                $address[$key] = $value;
            }
        }

        $a = new Addresses();
        $a->fromArray($address, \BasePeer::TYPE_FIELDNAME);

        return $a;
    }

    /**
     * Wrap delete() to allow us to set PDO connection
     * Never call this directly, it should _always_ be called through CoreBundle\Service\Model\OrdersService::deleteOrder
     *
     * @param PropelPDO|null $con
     */
    public function delete(PropelPDO $con = null)
    {
        if ($con) {
            $this->dbConn = $con;
        }

        parent::delete($con);
    }

} // Orders
