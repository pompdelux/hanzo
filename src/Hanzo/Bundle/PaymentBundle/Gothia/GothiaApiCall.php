<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Gothia;

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
        $logger = Hanzo::getInstance()->container->get('logger');

        $errorReporting = error_reporting(0);
        $client = AFSWS_Init( 'test' );

        $response = $client->call( $function, $request );
        error_log(__LINE__.':'.__FILE__.' '.$callString); // hf@bellcom.dk debugging
        error_log(__LINE__.':'.__FILE__.' '.print_r($response,1)); // hf@bellcom.dk debugging

        error_reporting($errorReporting);

        if ( $response === false || $client->fault )
        {
            $msg = 'Kunne ikke forbinde til Gothia Faktura service, prøv igen senere';

            $err = $client->getError();

            if ( $err )
            {
                $msg .= $err;
            }

            throw new GothiaApiCallException( $msg );
        }

        return new GothiaApiCallResponse( $response, $function );
    }

    /**
     * callAcquirersStatus
     * @param GothiaAccount $account
     * @return bool
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function checkCustomer( GothiaAccounts $account )
    {
        // FIXME: hardcoded values
        $callString = AFSWS_CheckCustomer(
	        $this->userString(),
            AFSWS_Customer(
                $account->getAddress(),
                $account->getCountryCode(),
                'SEK',
                '1000010',
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

        return true;
    }

    /**
     * placeReservation
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function placeReservation( GothiaAccounts $account, Orders $order )
    {
    }

    /**
     * cancelReservation
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancelReservation( GothiaAccounts $account, Orders $order )
    {
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
