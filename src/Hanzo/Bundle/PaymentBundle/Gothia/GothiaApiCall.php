<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Gothia;

use Exception;

use Hanzo\Core\Hanzo,
    Hanzo\Model\Orders,
    Hanzo\Model\Customers,
    Hanzo\Model\GothiaAccounts,
    Hanzo\Bundle\PaymentBundle\Gothia\GothiaApiCallResponse;

// Great... fucking oldschool crap code:
require 'AFWS.php';

class GothiaApiCall
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
     * @var GothiaApiCall instance 
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
     * @return GothiaApiCallResponse 
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function call( $function, $request )
    {
        //$logger = Hanzo::getInstance()->container->get('logger');

        $errorReporting = error_reporting(0);
        $client = AFSWS_Init( 'test' );

        $response = $client->call( $function, $request );
        error_log(__LINE__.':'.__FILE__.' '.$request); // hf@bellcom.dk debugging
        error_log(__LINE__.':'.__FILE__.' '.print_r($response,1)); // hf@bellcom.dk debugging
        error_reporting($errorReporting);

        // If there is a problem with the connection or something like that a GothiaApiCallException is thrown
        // Else a GothiaApiCallResponse is returned, which might still be an "error" but the call went through fine
        try
        {
            $this->checkResponseForErrors( $response, $client );
        }
        catch (Exception $e)
        {
            throw new GothiaApiCallException( $e->getMessage() );
        }

        return new GothiaApiCallResponse( $response, $function );
    }

    /**
     * checkResponseForErrors
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function checkResponseForErrors( $response, $client )
    {
        $errors = array();

        $prettyErrors = array(
            5000  => 'Kunne ikke forbinde til Gothia Faktura service, prøv igen senere',
            10004 => 'Tyvärr blev du inte godkänd i vår kontroll vid köp mot faktura.<br>Var vänlig kontrollera att du har angivit ditt namn, personnummer och folkbokföringsadress enligt folkbokföringens register korrekt, alternativt välj ett annat betalningssätt.',
            10006 => 'Tyvärr blev du inte godkänd i vår kontroll vid köp mot faktura.<br>Var vänlig kontrollera att du har angivit ditt namn, personnummer och folkbokföringsadress enligt folkbokföringens register korrekt, alternativt välj ett annat betalningssätt.',
        );

        if ( $response === false || $client->fault )
        {
            $msg = 'Kunne ikke forbinde til Gothia Faktura service, prøv igen senere';

            $err = $client->getError();

            if ( $err )
            {
                $msg .= $err;
            }

            throw new Exception( $msg );
        }

        foreach ( $response as $key => $data )
        {
            if ( isset($data['Errors']) && !empty($data['Errors']) && is_array($data['Errors']) )
            {
                foreach ( $data['Errors'] as $errorKey => $errorData )
                {
                    if ( !empty($errorData) )
                    {
                        if ( !isset($errorData['ID']) && isset($errorData[0]['ID']) )
                        {
                            foreach ( $errorData as $subError )
                            {
                                $errors[] = (isset( $prettyErrors[$subError['ID']] )) ? $prettyErrors[$subError['ID']] : $subError['Message'];
                            }
                        }
                        else
                        {
                            $errors[] = (isset( $prettyErrors[$errorData['ID']] )) ? $prettyErrors[$errorData['ID']] : $errorData['Message'];
                        }
                    }
                }
            }
            if ( isset($data['TemporaryExternalProblem']) && $data['TemporaryExternalProblem'] !== 'false' )
            {
                $errors[] = 'Kunne ikke forbinde til Gothia Faktura service, prøv igen senere';
            }
        }

        if (!empty($errors))
        {
            throw new Exception( implode('<br>', $errors) );
        }
    }


    /**
     * callAcquirersStatus
     * @param GothiaAccount $account
     * @return bool
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function checkCustomer( Customers $customer )
    {
        // FIXME: hardcoded values
        $callString = AFSWS_CheckCustomer(
	        $this->userString(),
            AFSWS_Customer(
                $customer->getAddress(),
                'SE',
                'SEK',
                $account->getCustomersId(),
                'Person',
                null,
                null,
                null,
                $account->getEmail(),
                null,
                $account->getFirstName(),
                $account->getLastName(),
                null,
                $account->getSocialSecurityNum(),
                $account->getPhone(),
                $account->getPostalCode(),
                null,
                null
            )
        );

        $response = $this->call('CheckCustomer', $callString);

        return $response;
    }

    /**
     * placeReservation
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function placeReservation( GothiaAccounts $account, Orders $order )
    {
        $amount = $order->getTotalPrice();
        // hf@bellcom.dk, 29-aug-2011: remove last param to Reservation, @see comment in cancelReservation function -->>
        $callString = AFSWS_PlaceReservation(
	        $this->userString(),
            AFSWS_Reservation('NoAccountOffer', $amount, 'SEK', $account->getCustomersId(), '') 
        );
        // <<-- hf@bellcom.dk, 29-aug-2011: remove last param to Reservation, @see comment in cancelReservation function

        $response = $this->call('CancelReservation', $callString);

        return $response;
    }

    /**
     * cancelReservation
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancelReservation( GothiaAccounts $account, Orders $order )
    {
        $amount = $order->getTotalPrice();
        // Gothia uses tns:CancelReservation which contains a tns:cancelReservation, therefore the 2 functions with almost the same name
        // hf@bellcom.dk, 29-aug-2011: remove 2.nd param to CancelReservationObj, pr request of Gothia... don't know why, don't care why :) -->>
        // hf@bellcom.dk, 21-jan-2012: 2.nd param was order no.
        $callString = AFSWS_CancelReservation(
	        $this->userString(),
            AFSWS_CancelReservationObj( $account->getCustomersId, '', $amount) 
        );
        // <<-- hf@bellcom.dk, 29-aug-2011: remove 2.nd param to CancelReservationObj, pr request of Gothia... don't know why, don't care why :)

        $response = $this->call('CancelReservation', $callString);

        return $response;
    }

    /**
     * userString
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function userString()
    {
        return AFSWS_User($this->settings['username'], $this->settings['password'], $this->settings['clientID']);
    }
}
