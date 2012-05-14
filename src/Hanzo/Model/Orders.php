<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use \BasePeer;
use \PropelPDO;
use \PropelCollection;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\om\BaseOrders;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersStateLog;

use Hanzo\Model\OrdersAttributes;
use Hanzo\Model\OrdersAttributesQuery;

use Hanzo\Model\OrdersVersions;
use Hanzo\Model\OrdersVersionsQuery;

use Hanzo\Model\ShippingMethods;

use Hanzo\Model\CustomerQuery;

use Exception;

/**
 * Skeleton subclass for representing a row from the 'orders' table.
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
    const STATE_ERROR_PAYMENT   = -110;
    const STATE_ERROR           = -100;
    const STATE_BUILDING        =  -50;
    const STATE_PRE_CONFIRM     =  -30; // nuke ???
    const STATE_PRE_PAYMENT     =  -20;
    const STATE_POST_PAYMENT    =   10; // nuke ??
    const STATE_PAYMENT_OK      =   20;
    const STATE_PENDING         =   30; // sidste "edit" step
    const STATE_BEING_PROCESSED =   40; // hos ax
    const STATE_SHIPPED         =   50; // lukket

    const TYPE_PRIVATE          =  -1;
    const TYPE_GIFT             =  -2;
    const TYPE_FRIEND           =  -3;
    const TYPE_OUTSIDE_EVENT    =  -4;
    const TYPE_NORMAL           = -10;

    private $state_message_map = array(
        self::STATE_ERROR_PAYMENT => 'Payment error',
        self::STATE_ERROR => 'General error',
        self::STATE_BUILDING => 'Building order',
        self::STATE_PRE_CONFIRM => 'Order in pre confirm state',
        self::STATE_PRE_PAYMENT => 'Order in pre payment state',
        self::STATE_POST_PAYMENT => 'Order in post confirm state',
        self::STATE_PAYMENT_OK => 'Order payment confirmed',
        self::STATE_PENDING => 'Order pending',
        self::STATE_BEING_PROCESSED => 'Order beeing processed',
        self::STATE_SHIPPED => 'Order shipped/done',
    );

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
        $data = array();
        $data['order'] = $this->toArray();
        unset($data['order']['Id']);

        $data['products'] = $this->getOrdersLiness()->toArray();
        $data['attributes'] = $this->getOrdersAttributess()->toArray();

        // find current version id.
        $version_id = OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->orderByVersionId('desc')
            ->findOne()
        ;

        if ($version_id) {
            $version_id = $version_id->getVersionId() + 1;
        } else {
            $version_id = 1;
        }

        $now = time();
        $version = new OrdersVersions();
        $version->setOrdersId($this->getId());
        $version->setVersionId($version_id);
        $version->setContent(serialize($data));
        $version->setCreatedAt($now);
        $version->save();

        // for the first version we create entries for both first and second version
        if ($version_id == 1) {
            $version = new OrdersVersions();
            $version->setOrdersId($this->getId());
            $version->setVersionId($version_id + 1);
            $version->setContent(serialize($data));
            $version->setCreatedAt($now);
            $version->save();
        }

        $this->setVersionId($version_id + 1);
        return $this->save();
    }


    /**
     * Get all version ids including the current version
     *
     * @return array
     */
    public function getVersionIds()
    {
        $versions = OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->orderByVersionId('desc')
            ->find()
        ;

        $id = $this->getVersionId();
        $ids = array(
            $id => $id
        );

        foreach ($versions as $version) {
            $ids[$version->getVersionId()] = $version->getVersionId();
        }

        return array_keys($ids);
    }


    /**
     * delete a version, note you cannot delete the current version
     *
     * @param  int $version_id the version you wich to delete
     * @return boolean
     */
    public function deleteVersion($version_id)
    {
        if (($version_id == $this->getVersionId()) || !in_array($version_id, $this->getVersionIds())) {
            throw new OutOfBoundsException('Invalid version id');
        }

        return OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->findOneByVersionId($version_id)
            ->delete()
        ;
    }


    /**
     * switch the order object to another version
     *
     * @param  int $version_id the version id to switch to
     * @return object Orders
     */
    public function toVersion($version_id)
    {
        // if it's the same version, just return self.
        if ($this->getVersionId() == $version_id) {
            return $this;
        }

        $version = OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->findOneByVersionId($version_id)
        ;

        if (!$version instanceof OrdersVersions) {
            throw new OutOfBoundsException('No such version: ' . $version_id . ' of order nr: ' . $this->getId());
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

        $collection = new PropelCollection();
        foreach ($data['attributes'] as $item) {
            $line = new OrdersAttributes();
            $line->fromArray($item);
            $collection->prepend($line);
        }
        $this->setOrdersAttributess($collection);

        // save and return the version
        return $this->save();
    }

    /**
     * getOrderAtVersion
     * Based on this->toVersion
     * @param int $version_id
     * @return Orders
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getOrderAtVersion( $version_id )
    {
        // if it's the same version, just return self.
        if ($this->getVersionId() == $version_id) {
            return $this;
        }

        $version = OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->findOneByVersionId($version_id)
            ;

        if (!$version instanceof OrdersVersions) {
            throw new OutOfBoundsException('No such version: ' . $version_id . ' of order nr: ' . $this->getId());
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

        //$collection = new PropelObjectCollection();
        foreach ($data['attributes'] as $item) {
            $line = new OrdersAttributes();
            $line->fromArray($item);
            $order->addOrdersAttributes($line);
            //$collection->prepend($line);
        }
        //$order->setOrdersAttributess($collection);

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
        if (count($this->getVersionIds()) < 2) {
            return $this;
        }

        $version = OrdersVersionsQuery::create()
            ->filterByOrdersId($this->getId())
            ->filterByVersionId($this->getVersionId(), \Criteria::NOT_EQUAL)
            ->orderByVersionId('desc')
            ->findOne()
            ;

        return $this->toVersion($version->getVersionId());
    }


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
        $line->setProductsColor($product->getColor());
        $line->setProductsSize($product->getSize());
        $line->setQuantity($quantity);
        $line->setPrice($price['price']);
        $line->setVat($price['vat']);
        $line->setType('product');
        $this->addOrdersLines($line);
    }

    /**
     * setOrderLineShipping
     *
     * TODO: rewrite to use self::setOrderLine
     *
     * @param ShippingMethod
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setOrderLineShipping( ShippingMethods $shippingMethod, $isFee = false )
    {
        if ( $isFee )
        {
            $price = $shippingMethod->getFee();
            $name  = $shippingMethod->getName();
            $id    = $shippingMethod->getFeeExternalId();
            $type  = 'shipping.fee';
        }
        else
        {
            $price = $shippingMethod->getPrice();
            $name  = $shippingMethod->getName();
            $id    = $shippingMethod->getExternalId();
            $type  = 'shipping';
        }


        // first update existing product lines, if any
        $lines = $this->getOrdersLiness();
        foreach ($lines as $index => $line)
        {
            if ( $line->getType() == $type )
            {
                $line->setProductsName( $name );
                $line->setProductsId( $id );
                $line->setPrice( $price );
                $line->setVat( 0.00 );
                $lines[$index] = $line;
                $this->setOrdersLiness($lines);

                return;
            }
        }

        $line = new OrdersLines;
        $line->setOrdersId($this->getId());
        $line->setProductsId( $id );
        $line->setProductsName( $name );
        $line->setQuantity(1);
        $line->setPrice( $price );
        $line->setVat( 0.00 );
        $line->setType( $type );
        $this->addOrdersLines($line);
    }

    public function getOrderLineShipping()
    {
        $shipping = array();
        foreach ($this->getOrdersLiness() as $index => $line) {
            if (in_array($line->getType(), array('shipping', 'shipping.fee'))) {
                $shipping[] = $line;
            }
        }

        return $shipping;
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
        $session = Hanzo::getInstance()->getSession();

        if(FALSE === $session->has('order_id')) {
            $session->set('order_id', $this->getId());
        }
    }


    public function getTotalPrice($products_only = false)
    {
        $lines = $this->getOrdersLiness();

        $total = 0;
        foreach ($lines as $line) {
            if ($products_only && $line->getType() != 'product') {
                continue;
            }

            $total += ($line->getPrice() * $line->getQuantity());
        }

        return $total;
    }

    public function getTotalVat()
    {
        $lines = $this->getOrdersLiness();

        $total = 0;
        foreach ($lines as $line) {
            $total += ($line->getVat() * $line->getQuantity());
        }

        return $total;
    }

    public function getTotalQuantity($products_only = false)
    {
        $lines = $this->getOrdersLiness();

        $total = 0;
        foreach ($lines as $line) {
            if ($products_only && $line->getType() != 'product') {
                continue;
            }

            $total += $line->getQuantity();
        }

        return $total;
    }

    /**
     * setAttribute
     *
     * Sets an order attribute
     *
     * @param string $key Name of the attribute
     * @param string $ns Namespace of the attribute, e.g. payment
     * @param string $value The value of the attribute
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function setAttribute( $key, $ns, $value )
    {
        $attributes = $this->getOrdersAttributess();

        // Update existing attributes
        foreach ($attributes as $index => $attribute)
        {
            if ( $attribute->getCKey() == $key && $attribute->getNs() == $ns )
            {
                $attribute->setCValue( $value );
                return;
            }
        }

        $attribute = new OrdersAttributes();
        $attribute->setCKey( $key );
        $attribute->setNs( $ns );
        $attribute->setCValue( $value );

        $this->addOrdersAttributes($attribute);
    }

    /**
     * setPaymentMethod
     * @param string $method
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setPaymentMethod( $method )
    {
        $this->setBillingMethod( $method );
    }

    /**
     * setPaymentPaytype
     * @param string $paytype
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setPaymentPaytype( $paytype )
    {
        // TODO: match CustPaymMode from old system?
        $this->setAttribute( 'paytype', 'payment', $paytype );
    }

    /**
     * setOrderLinePaymentFee
     *
     * TODO: rewrite to use self::setOrderLine
     *
     * @param string $name
     * @param float $price
     * @param float $vat
     * @param string $id
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setOrderLinePaymentFee( $name, $price, $vat, $id )
    {
        $type = 'payment.fee';

        // first update existing product lines, if any
        $lines = $this->getOrdersLiness();
        foreach ($lines as $index => $line)
        {
            if ( $line->getProductsId() == $id && $line->getType() == $type )
            {
                $line->setProductsName( $name );
                $line->setPrice( $price );
                $line->setVat( $vat );
                $lines[$index] = $line;
                $this->setOrdersLiness($lines);

                return;
            }
        }

        $line = new OrdersLines;
        $line->setOrdersId($this->getId());
        $line->setProductsId( $id );
        $line->setProductsName( $name );
        $line->setQuantity(1);
        $line->setPrice( $price );
        $line->setVat( $vat );
        $line->setType( $type );
        $this->addOrdersLines($line);
    }


    /**
     * set an orderline
     * note if the type is not "product" only one line pr. type is handled
     *
     * @param string $type  the line type
     * @param int    $id    product id, must be set even for virtual lines
     * @param string $name  line description
     * @param float  $price price
     * @param float  $vat   vat
     * @return object Orders object returned to keep the chain alive.
     */
    public function setOrderLine($type, $id, $name, $price = 0.00, $vat = 0.00, $quantity = 1)
    {
        $lines = $this->getOrdersLiness();

        foreach ($lines as $index => $line) {
            if ($line->getType() == $type) {
                if ($type != 'product') {
                    $line->setProductsId( $id );
                    $line->setProductsName( $name );
                    $line->setPrice( $price );
                    $line->setVat( $vat );
                    $line->setQuantity( $quantity );
                    $lines[$index] = $line;
                    $this->setOrdersLiness($lines);

                    // maintain chain, return self
                    return $this;
                } else {
                    if ($line->getProductsId() == $id) {
                        $line->setProductsName( $name );
                        $line->setPrice( $price );
                        $line->setVat( $vat );
                        $line->setQuantity( $quantity );
                        $lines[$index] = $line;
                        $this->setOrdersLiness($lines);

                        // maintain chain, return self
                        return $this;
                    }
                }
            }
        }

        // add new line
        $line = new OrdersLines;
        $line->setType( $type );
        $line->setOrdersId($this->getId());
        $line->setProductsId( $id );
        $line->setProductsName( $name );
        $line->setQuantity( $quantity );
        $line->setPrice( $price );
        $line->setVat( $vat );
        $this->addOrdersLines($line);

        // maintain chain, return self
        return $this;
    }


    /**
     * setShippingMethod
     * @param string $method
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setShippingMethod( $method )
    {
        $this->setDeliveryMethod( $method );
    }

    /**
     * setBillingAddress
     * @param Addresses $address
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setBillingAddress( Addresses $address )
    {
        if ( $address->getType() != 'payment' )
        {
            throw new Exception( 'Address is not of type payment' );
        }

        $this->setBillingAddressLine1( $address->getAddressLine1() )
            ->setBillingAddressLine2( $address->getAddressLine2() )
            ->setBillingCity( $address->getCity() )
            ->setBillingPostalCode( $address->getPostalCode() )
            ->setBillingCountry( $address->getCountry() )
            ->setBillingCountriesId( $address->getCountriesId() )
            ->setBillingStateProvince( $address->getStateProvince() )
            ->setBillingCompanyName( $address->getCompanyName() )
            ->setBillingFirstName( $address->getFirstName() )
            ->setBillingLastName( $address->getLastName() )
            ;
    }

    /**
     * clearBillingAddress
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function clearBillingAddress()
    {
        $fields = array(
            'BillingAddressLine1'  => null,
            'BillingAddressLine2'  => null,
            'BillingCity'          => null,
            'BillingPostalCode'    => null,
            'BillingCountry'       => null,
            'BillingCountriesId'   => null,
            'BillingStateProvince' => null,
            'BillingCompanyName'   => null,
            'BillingFirstName'     => null,
            'BillingLastName'      => null,
        );

        $this->fromArray($fields);
    }

    /**
     * clearBillingAddress
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function clearDeliveryAddress()
    {
        $fields = array(
            'DeliveryAddressLine1'  => null,
            'DeliveryAddressLine2'  => null,
            'DeliveryCity'          => null,
            'DeliveryPostalCode'    => null,
            'DeliveryCountry'       => null,
            'DeliveryCountriesId'   => null,
            'DeliveryStateProvince' => null,
            'DeliveryCompanyName'   => null,
            'DeliveryFirstName'     => null,
            'DeliveryLastName'      => null,
        );

        $this->fromArray($fields);
    }

    /**
     * setDeliveryAddress
     *
     * @param Addresses $address
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setDeliveryAddress( Addresses $address )
    {
        if ( $address->getType() != 'shipping' )
        {
            throw new Exception( 'Address is not of type shipping' );
        }

        $this->setDeliveryAddressLine1( $address->getAddressLine1() )
            ->setDeliveryAddressLine2( $address->getAddressLine2() )
            ->setDeliveryCity( $address->getCity() )
            ->setDeliveryPostalCode( $address->getPostalCode() )
            ->setDeliveryCountry( $address->getCountry() )
            ->setDeliveryCountriesId( $address->getCountriesId() )
            ->setDeliveryStateProvince( $address->getStateProvince() )
            ->setDeliveryCompanyName( $address->getCompanyName() )
            ->setDeliveryFirstName( $address->getFirstName() )
            ->setDeliveryLastName( $address->getLastName() )
            ;
    }

    /**
     * Fetch an array of attached documents.
     *
     * @return array
     */
    public function getAttachments()
    {
        $attributes = $this->getOrdersAttributess();

        $attachments = array();
        foreach ($attributes as $attribute) {
            if ($attribute->getNs() == 'attachment') {
                $attachments[$attribute->getCKey()] = $attribute->getCValue();
            }
        }

        return $attachments;
    }

    public function getAttributes()
    {
        $attributes = new \stdClass();
        foreach ($this->getOrdersAttributess() as $attr) {
            $ns = str_replace(array(':', '.'), '_', $attr->getNs());

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
     * @param int $gateway_id if specified, this is used over the auto generated one
     * @return Orders The current object (for fluent API support)
     */
    public function setPaymentGatewayId($gateway_id = null)
    {
        // Should probably be handled by the payment method
        if (false !== strpos($gateway_id, '_')) {
            list($junk, $gateway_id) = explode('_', $gateway_id, 2);
        }

        return parent::setPaymentGatewayId($gateway_id);
    }


    /**
     * Wrapping the setState method to log all state changes
     *
     * @param int $v state id
     * @return Orders The current object (for fluent API support)
     */
    public function setState($v)
    {
        $log = new OrdersStateLog();
        $log->setOrdersId($this->getId());
        $log->setState($v);
        $log->setMessage($this->state_message_map[$v]);
        $log->setCreatedAt(time());

        $this->addOrdersStateLog($log);

        return parent::setState($v);
    }


    /**
     * Check wether a product is in the "cart" or not
     *
     * @param  mixed $product_id id or sku of the product
     * @return boolean             [description]
     */
    public function hasProduct($product_id)
    {
        $isInt = preg_match('/^[0-9]+$/', $product_id);
        foreach ($this->getOrdersLiness() as $line) {
            if ($isInt) {
                if ($line->getProductsId() == $product_id) {
                    return true;
                }
            } else {
                // note the "name" here is the same as the master "sku"
                if ($line->getProductsName() == $product_id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * cancelPayment
     *
     * Cancels the payment for the specific order, so you don't have to know which payment method was used
     *
     * @return bool
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancelPayment( )
    {
        // FIXME: enable
        /*if ( $this->getState() != self::STATE_PENDING )
        {
            throw new Exception('Not possible to cancel payment on an order in state "'.$this->getState().'"');
        }*/

        $attributes = $this->getOrdersAttributess();

        $paytype = false;

        foreach ($attributes as $attribute)
        {
            if ( $attribute->getNs() == 'payment' && $attribute->getCKey() == 'paytype' )
            {
                $paytype = $attribute->getCValue();
            }
        }

        if ( $paytype === false)
        {
            throw new Exception( 'No paytype registered on order with id: "'.$this->getId().'"' );
        }

        $api = Hanzo::getInstance()->container->get('payment.'.$paytype.'api');

        $customer = CustomersQuery::create()->findOneById( $this->getCustomersId() );

        $api->call()->cancel( $customer, $this );
    }
} // Orders
