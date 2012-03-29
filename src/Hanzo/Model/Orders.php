<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use \PropelPDO;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\om\BaseOrders,
    Hanzo\Model\OrdersLines,
    Hanzo\Model\OrdersLinesPeer,
    Hanzo\Model\OrdersLinesQuery
    ;

use Hanzo\Model\ShippingMethods
    ;

use Exception
    ;

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
    const STATE_PRE_CONFIRM     =  -30;
    const STATE_PRE_PAYMENT     =  -20;
    const STATE_POST_PAYMENT    =   10;
    const STATE_PAYMENT_OK      =   20;
    const STATE_PENDING         =   30;
    const STATE_BEING_PROCESSED =   40;
    const STATE_SHIPPED         =   50;

    const TYPE_PRIVATE          =  -1;
    const TYPE_GIFT             =  -2;
    const TYPE_FRIEND           =  -3;
    const TYPE_OUTSIDE_EVENT    =  -4;
    const TYPE_NORMAL           = -10;


    protected $new_version = false;


    /**
     * Call this if you need to create a new version of the order.
     *
     * @return object Orders
     */
    public function addNewVersion()
    {
        $this->new_version = true;

        return $this;
    }


    /**
     * Return wether or not to version an order
     *
     * @return boolean
     */
    public function isVersioningNecessary($con = null)
    {
        return ($this->new_version && parent::isVersioningNecessary());
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
        $line->setTax($price['vat']);
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
                $line->setTax( 0.00 );
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
        $line->setTax( 0.00 );
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

    public function getTotalTax()
    {
        $lines = $this->getOrdersLiness();

        $total = 0;
        foreach ($lines as $line) {
            $total += ($line->getTax() * $line->getQuantity());
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
     * @param float $tax
     * @param string $id
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setOrderLinePaymentFee( $name, $price, $tax, $id )
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
                $line->setTax( $tax );
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
        $line->setTax( $tax );
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
     * @param float  $tax   tax
     * @return object Orders object returned to keep the chain alive.
     */
    public function setOrderLine($type, $id, $name, $price = 0.00, $tax = 0.00, $quantity = 1)
    {
        $lines = $this->getOrdersLiness();

        foreach ($lines as $index => $line) {
            if ($line->getType() == $type) {
                if ($type != 'product') {
                    $line->setProductsId( $id );
                    $line->setProductsName( $name );
                    $line->setPrice( $price );
                    $line->setTax( $tax );
                    $line->setQuantity( $quantity );
                    $lines[$index] = $line;
                    $this->setOrdersLiness($lines);

                    // maintain chain, return self
                    return $this;
                } else {
                    if ($line->getProductsId() == $id) {
                        $line->setProductsName( $name );
                        $line->setPrice( $price );
                        $line->setTax( $tax );
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
        $line->setTax( $tax );
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

    /**
     * Wrapping the setPaymentGatewayId method to auto-generate gateway id's
     *
     * @param int $gateway_id if specified, this is used over the auto generated one
     * @return Orders The current object (for fluent API support)
     */
    public function setPaymentGatewayId($gateway_id = null)
    {
        if (false !== strpos($gateway_id, '_')) {
            list($junk, $gateway_id) = explode('_', $gateway_id, 2);
        }

        return parent::setPaymentGatewayId($gateway_id);
    }

} // Orders
