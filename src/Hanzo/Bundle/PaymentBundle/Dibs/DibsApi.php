<?php

namespace Hanzo\Bundle\PaymentBundle\Dibs;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCall,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException;

class DibsApi
{
  /**
   * undocumented class variable
   *
   * @var bool
   **/
  const USE_AUTH_HEADERS = true;

  /**
   * undocumented class variable
   *
   * @var array 
   **/
  protected $settings = array();

  /**
   * __construct
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function __construct()
  {
    $this->settings = array(
      'md5key1' => '',
      'md5key2' => '',
      );
  }

  /**
   * callAcquirersStatus
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function callAcquirersStatus( $acquirer = 'all' )
  {
    $params = array(
      'replytype' => 'html',
      'acquirer'  => $acquirer,
      );

    $this->call( 'status.pml', $params );
  }

  /**
   * call
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  protected function call( $url, array $params, $useAuthHeaders = false, $rawResponse = false )
  {
    $response = DibsApiCall::getInstance($this->settings)->execute( $url, $params, $useAuthHeaders );
  }

  /**
   * Calculate md5 sum for verification
   * @return string 
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function md5( $orderID, $currency, $amount )
  {
    return md5( $this->settings['md5key2'] . md5( $this->settings['md5key1'] .'merchant='. $this->settings['merchant_id'] .'&orderid='. $orderID .'&currency='.$currency.'&amount='.$amount));
  }

  /**
   * md5AuthKey
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function md5AuthKey( $transact, $amount, $currency )
  {
    return md5( $this->settings['md5key2'] . md5( $this->settings['md5key1'] .'transact='.$transact.'&amount='.$amount.'&currency='.$currency));
  }

  /**
   * formatAmount
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  protected function formatAmount( $amount )
  {
    $amount = ( number_format( $amount, 2, '.', '') ) * 100 ; 
    return $amount;
  }

}
