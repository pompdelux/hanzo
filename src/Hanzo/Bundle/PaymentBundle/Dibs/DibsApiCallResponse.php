<?php

namespace Hanzo\Bundle\PaymentBundle\Dibs;

class DibsApiCallResponse
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
    $elements = explode('&', $rawResponse);

    foreach( $elements as $element )
    {
      $key = $value = '';
      list($key,$value) = explode('=',$element);
      $value = urldecode($value);
      if ( !empty($key) && !empty($value) )
      {
        $this->data[$key] = $value;
      }
    }

    $this->data['raw_response'] = $rawResponse;

    if ( empty($this->data) )
    {
      throw new DibsApiCallException( 'Could not parse response "'.$rawResponse.'" from DIBS' );
    }
  }

  /**
   * setStatus
   *
   * Maps the status codes returned from dibs to a description
   * The argument contains: 
   * - actioncode: A 2-3 letter/digit code, see "Capture" at http://tech.dibs.dk/10-step-guide/10-step-guide/5-your-own-test/ where for example d02 is returned when card number xxxx100000000002 has been used
   * - status: an id, either an int or DECLINED
   *
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  protected function setStatus( $function )
  {
    switch ($function) 
    {
      case 'cgi-adm/payinfo.cgi':
        $codes = array(
          0  => 'transaction inserted (not approved)',
          1  => 'declined',
          2  => 'authorization approved',
          3  => 'capture sent to acquirer',
          4  => 'capture declined by acquirer',
          5  => 'capture completed',
          6  => 'authorization deleted',
          7  => 'capture balanced',
          8  => 'partially refunded and balanced',
          9  => 'refund sent to acquirer',
          10 => 'refund declined',
          11 => 'refund completed',
          12 => 'capture pending',
          13 => 'ticket" transaction',
          14 => 'deleted "ticket" transaction',
          15 => 'refund pending',
          16 => 'waiting for shop approval',
          17 => 'declined by DIBS',
          18 => 'multicap transaction open',
          19 => 'multicap transaction closed',
        );

        if ( !isset($this->data['status']) )
        {
          throw new DibsApiCallException( 'Missing status in response from Dibs' );
        }

        if ( !isset( $codes[$this->data['status']] ) )
        {
          throw new DibsApiCallException( 'Unknown status code: "'. $this->data['status'] .'" for: "'. $function .'"');
        }

        $this->data['status_description'] = $codes[$this->data['status']];

        break;

      default:
        // code...
        break;
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
