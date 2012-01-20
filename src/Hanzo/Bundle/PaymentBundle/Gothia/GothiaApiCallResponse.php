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
    public $isError = false;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $rawResponse, $function, $client )
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
        $prettyErrors = array(
            5000  => 'Kunne ikke forbinde til Gothia Faktura service, prøv igen senere',
            10004 => 'Tyvärr blev du inte godkänd i vår kontroll vid köp mot faktura.<br>Var vänlig kontrollera att du har angivit ditt namn, personnummer och folkbokföringsadress enligt folkbokföringens register korrekt, alternativt välj ett annat betalningssätt.',
            10006 => 'Tyvärr blev du inte godkänd i vår kontroll vid köp mot faktura.<br>Var vänlig kontrollera att du har angivit ditt namn, personnummer och folkbokföringsadress enligt folkbokföringens register korrekt, alternativt välj ett annat betalningssätt.',
        );

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
                                $this->errors[] = (isset( $prettyErrors[$subError['ID']] )) ? $prettyErrors[$subError['ID']] : $subError['Message'];
                            }
                        }
                        else
                        {
                            $this->errors[] = (isset( $prettyErrors[$errorData['ID']] )) ? $prettyErrors[$errorData['ID']] : $errorData['Message'];
                        }
                        error_log(__LINE__.':'.__FILE__.' '.print_r($errorData,1)); // hf@bellcom.dk debugging
                    }
                }
            }
            if ( isset($data['TemporaryExternalProblem']) && $data['TemporaryExternalProblem'] !== 'false' )
            {
                $this->errors[] = 'Kunne ikke forbinde til Gothia Faktura service, prøv igen senere';
            }
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
