<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle\ShippingMethods;

/**
 * undocumented class
 *
 * @packaged default
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class ShippingMethod
{
    const TYPE_FEE = true;
    const TYPE_NORMAL = false;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $name;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $carrier;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $description;

    /**
     * Internal id
     *
     * @var string
     **/
    protected $id;

    /**
     * AX id 
     *
     * @var string
     **/
    protected $externalId;

    /**
     * CalculationEngine 
     *
     * @var string
     **/
    protected $calcEngine;

    /**
     * __construct
     * @param string $carrier
     * @param string $name
     * @param string $description
     * @param string $externalId
     * @param mixed $calcEngine
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $carrier, $name, $description, $externalId, $calcEngine )
    {
        $this->carrier = $carrier;
        $this->name = $name;
        $this->description = $description;
        $this->externalId = $externalId;
        $this->calcEngine = $calcEngine;

        // FIXME: hardcoded
        $this->feeExternalId = 90;
        $this->hasFee = true;
        $this->fee = 10.00;
        $this->feeTax = 0.00;
        $this->feeName = 'eks. gebyr';
    }

    /**
     * get
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getName()
    {
        return $this->name;
    }

    /**
     * getExternalId
     * @return string The AX id
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * getPrice 
     * @return float
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getPrice()
    {
        // FIXME: hardcoded
        return 100.00;
    }

    /**
     * getFeePrice
     * @return float
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFeePrice()
    {
        return $this->fee;
    }

    /**
     * getFeeTax
     * @return float
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFeeTax()
    {
        return $this->feeTax;
    }

    /**
     * hasFee
     * @return bool
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function hasFee()
    {
        return $this->hasFee;
    }

    /**
     * getFeeExternalId
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFeeExternalId()
    {
        return $this->feeExternalId;
    }

    /**
     * getFeeName
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getFeeName()
    {
        return $this->feeName;
    }

    /**
     * getTax
     * @return float
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getTax()
    {
        // FIXME: hardcoded
        return 0.00;
    }

} // END class ShippingMethod
