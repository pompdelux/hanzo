<?php

namespace Hanzo\Bundle\PaymentBundle\Dibs;

use Hanzo\Core\Hanzo,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApiCallResponse;

class DibsApiCall
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
   * @var DibsApiCall instance 
   **/
  private static $instance = null;

  /**
   * undocumented class variable
   *
   * @var string
   **/
  protected $baseUrl = 'https://payment.architrade.com/';

  /**
   * __construct
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  private function __construct() {}

  /**
   * someFunc
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
    public static function getInstance($settings)
    {
      if ( self::$instance === null )
      {
        self::$instance = new self;
      }

      self::$instance->settings = $settings;

      return self::$instance;
    }

  /**
   * call
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  protected function call( $function, array $params, $useAuthHeaders = false )
  {
    $logger = Hanzo::getInstance()->container->get('logger');

    $ch = curl_init();

    $url = $this->baseUrl . $function;

    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

    $headers = array();

    if ( $useAuthHeaders )
    {
      if ( !isset($this->settings['api_user']) || !isset($this->settings['api_pass']) )
      {
        throw new DibsApiCallException( 'DIBS api: Missing api username or/and password' );
      }

      $headers = array( 'Authorization: Basic '. base64_encode($this->settings['api_user'].':'.$this->settings['api_pass']) );

      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
    }

    //if ( $this->debug )
    //{
      //$msg = 'Calling Dibs api on url: '.$url.PHP_EOL.'Params:'.print_r($headers,1).PHP_EOL.print_r($params,1);
      //$logger->debug($msg)
    //}
    //$this->debugMsg( 'Action: "'. $this->currentAction .'" data: '. print_r($params,1) , __FUNCTION__, __LINE__ );

    $response = curl_exec($ch);

    curl_close($ch);

    if ( $response === false )
    {
      throw new DibsApiCallException('Kommunikation med DIBS fejlede, fejlen var: "'.curl_error($ch).'"');
    }

    return new DibsApiCallResponse( $response, $function );
  }

  /**
   * callAcquirersStatus
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function acquirersStatus( $acquirer = 'all' )
  {
    $params = array(
      'replytype' => 'html',
      'acquirer'  => $acquirer,
      );

    return $this->call( 'status.pml', $params );
  }

  /**
   * cancelPayment
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function cancelPayment( $transactionID )
  {
  }

  /**
   * someFunc
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function payinfo( $transactionID )
  {
    $params = array(
      'transact'  => $transactionID,
      );

    return $this->call( 'cgi-adm/payinfo.cgi', $params, self::USE_AUTH_HEADERS );
  }
}
