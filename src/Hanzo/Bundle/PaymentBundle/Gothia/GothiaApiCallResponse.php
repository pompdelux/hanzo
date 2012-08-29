<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Gothia;

use Hanzo\Core\Tools;

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
    protected $isError = false;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $rawResponse, $function )
    {
        $this->parse( $rawResponse, $function );
    }

    /**
     * isError
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function isError()
    {
        return $this->isError;
    }

    /**
     * setIsError
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setIsError()
    {
        $this->isError = true;
    }

    /**
     * parse
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function parse( $rawResponse, $function )
    {
        switch ($function) 
        {
            case 'CheckCustomer':
                if ( !isset( $rawResponse['CheckCustomerResult']['Success'] ) )
                {
                    $this->isError = true;
                }
                else
                {
                    if ( $rawResponse['CheckCustomerResult']['Success'] !== 'true' ) 
                    {
                        $this->isError = true;
                    }
                }

                if ( !isset( $rawResponse['CheckCustomerResult']['Customer'] ) )
                {
                    $this->isError = true;
                }

                if ( !$this->isError )
                {
                    foreach ($rawResponse['CheckCustomerResult']['Customer'] as $key => $value) 
                    {
                        $this->data[$key] = $value;
                    }
                }

                if ( isset($this->data['PurchaseStop']) && $this->data['PurchaseStop'] === 'true' )
                {
                    $this->isError = true;
                }

                break;
            case 'CancelReservation':
                if ( !isset($rawResponse['CancelReservationResult']['Success']) || $rawResponse['CancelReservationResult']['Success'] !== 'true')
                {
                    $this->isError = true;
                }

                if ( !$this->isError )
                {
                    if ( !isset($rawResponse['CancelReservationResult']['Reservation']) )
                    {
                        $this->isError = true;
                        
                        Tools::debug( 'Missing field (Reservation)', __METHOD__, $rawResponse );
                    }
                    else
                    {
                        foreach ($rawResponse['CancelReservationResult']['Reservation'] as $key => $value) 
                        {
                            $this->data[$key] = $value;
                        }
                    }
                }
                break;
            case 'PlaceReservation':
                if ( !isset($rawResponse['PlaceReservationResult']['ReservationApproved']) || $rawResponse['PlaceReservationResult']['ReservationApproved'] !== 'true')
                {
                    $this->isError = true;
                }

                if ( !$this->isError )
                {
                    foreach ($rawResponse['PlaceReservationResult']['Reservation'] as $key => $value) 
                    {
                        $this->data[$key] = $value;
                    }
                }
                break;
        }
    }
}
