<?php

namespace Hanzo\Bundle\PaymentBundle\Dibs;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCall,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallException;

class DibsApi
{
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
    // FIXME: hardcoded settings
    $this->settings = array(
      'md5key1' => 'd|y3,Wxe5dydME)q4+0^BilEVfT[WuSp',
      'md5key2' => 'Q+]FJ]0FMvsyT,_GEap39LlgIr1Kx&n[',
      'merchant_id' => '90057323',
      'api_user' => 'bellcom_test_api_user',
      'api_pass' => '7iuTR8EZ',
      );
  }

  /**
   * call
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function call()
  {
    return DibsApiCall::getInstance($this->settings);
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
