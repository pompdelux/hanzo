<?php /* vim: set sw=4: */

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
    protected $isError = false;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $rawResponse, $function )
    {
        $this->parse( $rawResponse, $function );
        //$this->setStatus( $function );
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
     * parse
     * @todo: store info in $this->data
     *        should exceptions be thrown? dont think so
     *        move errors info the respective calls
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function parse( $rawResponse, $function )
    {
        switch ($function) 
        {
            case 'CheckCustomer':
                if ( isset( $rawResponse['CheckCustomerResult']['Success'] ) && $rawResponse['CheckCustomerResult']['Success'] === 'true' )
                {
                    $gothiaCustomer = $rawResponse['CheckCustomerResult']['Customer'];

                    if ( $gothiaCustomer['PurchaseStop'] === 'true' )
                    {
                        throw new Exception(GOTHIA_ERROR_PURCHASE_DENIED);
                    }
                }
                else
                {
                    throw new Exception(GOTHIA_ERROR_COULD_NOT_CREATE_CUSTOMER);
                }
                break;
            case 'CancelReservation':
                if ( !isset($rawResponse['CancelReservationResult']['Success']) || $rawResponse['CancelReservationResult']['Success'] != 'true')
                {
                    throw new Exception('Reservation kunne ikke annuleres');
                }
                break;
            case 'PlaceReservation':
                if ( !isset($rawResponse['PlaceReservationResult']['ReservationApproved']) || $rawResponse['PlaceReservationResult']['ReservationApproved'] != 'true')
                {
                    throw new Exception(GOTHIA_ERROR_RESERVATION_DENIED);
                }
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
