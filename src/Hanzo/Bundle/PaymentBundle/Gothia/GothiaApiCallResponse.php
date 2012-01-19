<?php

namespace Hanzo\Bundle\PaymentBundle\Gothia;

class GothiaApiCallResponse
{
  /**
   * undocumented class variable
   *
   * @var string
   **/
  public $data;

  /**
   * undocumented class variable
   *
   * @var string
   **/
  public $isError = false;

  /**
   * __construct
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function __construct( $rawResponse, $function )
  {
    $this->parse( $rawResponse );
    $this->setStatus( $function );
  }

  /**
   * parse
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  protected function parse( $rawResponse )
  {
    $this->data['raw_response'] = $rawResponse;

    if ( empty($this->data) )
    {
      throw new GothiaApiCallException( 'Could not parse response "'.$rawResponse.'" from DIBS' );
    }
  }

  /**
   * debug
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function debug()
  {
    return $this->data;
  }
}
