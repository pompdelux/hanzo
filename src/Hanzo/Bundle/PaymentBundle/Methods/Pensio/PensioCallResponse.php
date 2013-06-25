<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use SimpleXMLElement;

class PensioCallResponse
{
    protected $is_error = false;
    protected $error_message = '';
    protected $xml = '';
    protected $headers;

    /**
     * constructor
     *
     * @param array            $headers http_response_header see: http://php.net/manual/en/reserved.variables.httpresponseheader.php
     * @param SimpleXMLElement $xml     xml response object
     */
    public function __construct($headers, SimpleXMLElement $xml)
    {
        $this->headers = $headers;

        if (((string) $xml->Header[0]->ErrorCode[0]) != '0') {
            $this->is_error = true;
            $this->error_message = (string) $xml->Header[0]->ErrorMessage[0];
        } else {
            if (((string) $xml->Body[0]->Error[0]) == 'Error') {
                $this->is_error = true;
                $this->error_message = (string) $xml->Body[0]->CardHolderErrorMessage[0];
            }
        }

        $this->xml = $xml;
    }


    /**
     * did the call fail
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->is_error;
    }


    /**
     * get debugging information
     *
     * @return array
     */
    public function debug()
    {
        return [
            'headers' => $this->headers,
            'raw_response' => $this->xml,
            'reason' => $this->error_message,
            'status' => !$this->is_error,
            'status_description' => $this->error_message,
            'status_is_error' => $this->is_error,
        ];
    }
}
