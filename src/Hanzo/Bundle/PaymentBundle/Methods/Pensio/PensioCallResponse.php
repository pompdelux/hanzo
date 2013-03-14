<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use SimpleXMLElement;

class PensioCallResponse
{
    protected $is_error = false;
    protected $error_message = '';
    protected $xml = '';

    /**
     * constructor
     *
     * @param SimpleXMLElement $xml xml response object
     */
    public function __construct(SimpleXMLElement $xml)
    {
        if (((string) $xml->Header[0]->ErrorCode[0]) != '0') {
            $this->is_error = true;
            $this->error_message = (string) $xml->Header[0]->ErrorMessage[0];
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
            'status' => $this->is_error,
            'reason' => $this->error_message,
            'status_is_error' => $this->is_error,
            'status_description' => $this->error_message,
            'raw_response' => $this->xml,
        ];
    }
}
