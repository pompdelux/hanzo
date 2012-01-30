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
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function getExternalId()
  {
    return $this->externalId;
  }

} // END class ShippingMethod
