<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\Pensio;

use SimpleXMLElement;

/**
 * Class PensioCallResponse
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\Pensio
 */
class PensioCallResponse
{
    protected $isError = false;
    protected $errorMessage = '';
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

        if (((string) $xml->Header->ErrorCode) != '0') {
            $this->isError = true;
            $this->errorMessage = (string) $xml->Header->ErrorMessage;
        } else {
            if (((string) $xml->Body->Result) == 'Error') {
                $this->isError = true;
                $this->errorMessage = (string) $xml->Body->CardHolderErrorMessage;
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
        return $this->isError;
    }


    /**
     * get debugging information
     *
     * @return array
     */
    public function debug()
    {
        return [
            'headers'            => $this->headers,
            'raw_response'       => $this->xml,
            'reason'             => $this->errorMessage,
            'status'             => !$this->isError,
            'status_description' => $this->errorMessage,
            'status_is_error'    => $this->isError,
        ];
    }

    /**
     * @return SimpleXMLElement|string
     */
    public function getXml()
    {
        return $this->xml;
    }
}
