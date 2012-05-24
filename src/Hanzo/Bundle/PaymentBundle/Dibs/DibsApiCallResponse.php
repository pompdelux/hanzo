<?php /* vim: set sw=4: */

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
    protected $isError = false;

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
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function parse( $rawResponse )
    {
        $rawResponse = trim($rawResponse);

        if ( strpos($rawResponse,'&') === false )
        {
            throw new DibsApiCallException( 'Could not parse response "'.$rawResponse.'" from DIBS' );
        }

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
            case 'cgi-bin/transinfo.cgi':
                $codes = array(
                     0 => 'transaction inserted (not approved)',
                     1 => 'declined',
                     2 => 'authorization approved',
                     3 => 'capture sent to acquirer',
                     4 => 'capture declined by acquirer',
                     5 => 'capture completed',
                     6 => 'authorization deleted',
                     7 => 'capture balanced',
                     8 => 'partially refunded and balanced',
                     9 => 'refund sent to acquirer',
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
                break;

            // auth.cgi, reauth.cgi and ticket_auth.cgi
            case 'cgi-ssl/auth.cgi':
            case 'cgi-bin/reauth.cgi':
            case 'cgi-ssl/ticket_auth.cgi':
                $codes = array(
                     0 => 'Rejected by acquirer.',
                     1 => 'Communication problems.',
                     2 => 'Error in the parameters sent to the DIBS server. An additional parameter called "message" is returned, with a value that may help identifying the error.',
                     3 => 'Error at the acquirer.',
                     4 => 'Credit card expired.',
                     5 => 'Your shop does not support this credit card type, the credit card type could not be identified, or the credit card number was not modulus correct.',
                     6 => 'Instant capture failed.',
                     7 => 'The order number (orderid) is not unique.',
                     8 => 'There number of amount parameters does not correspond to the number given in the split parameter.',
                     9 => 'Control numbers (cvc) are missing.',
                    10 => 'The credit card does not comply with the credit card type.',
                    11 => 'Declined by DIBS Defender.',
                    20 => 'Cancelled by user at 3D Secure authentication step',
                );
                break;

            // capture.cgi, refund.cgi, cancel.cgi, and changestatus.cgi
            case 'cgi-adm/cancel.cgi':
            case 'cgi-bin/capture.cgi':
            case 'cgi-adm/changestatus.cgi':
            case 'cgi-adm/refund.cgi':
                $codes = array(
                     0 => 'Accepted',
                     1 => 'No response from acquirer.',
                     2 => 'Timeout',
                     3 => 'Credit card expired.',
                     4 => 'Rejected by acquirer.',
                     5 => 'Authorisation older than 7 days.',
                     6 => 'Transaction status on the DIBS server does not allow capture.',
                     7 => 'Amount too high.',
                     8 => 'Error in the parameters sent to the DIBS server. An additional parameter called "message" is returned, with a value that may help identifying the error.',
                     9 => 'Order number (orderid) does not correspond to the authorisation order number.',
                    10 => 'Re-authorisation of the transaction was rejected.',
                    11 => 'Not able to communicate with the acquier.',
                    12 => 'Confirm request error',
                    14 => 'Capture is called for a transaction which is pending for batch - i.e. capture was already called',
                    15 => 'Capture was blocked by DIBS.',
                );
                break;

            default:
                // code...
                return;
                break;
        }

        if ( !isset($this->data['status']) ) {
            throw new DibsApiCallException( 'Missing status in response from Dibs' );
        }

        if ( !isset( $codes[$this->data['status']] ) ) {
            throw new DibsApiCallException( 'Unknown status code: "'. $this->data['status'] .'" for: "'. $function .'"');
        }

        $this->data['status_description'] = $codes[$this->data['status']];
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
