<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\PayPal;

use Psr\Log\LoggerInterface;

/**
 * Class PayPalCallResponse
 *
 * @package Hanzo\Bundle\PaymentBundle\Methods\PayPal
 */
class PayPalCallResponse
{
    protected $isError = false;
    protected $parameters = [];

    protected $response;
    protected $responseHeaders;
    protected $errorMessage;

    /**
     * @param array           $responseHeaders
     * @param string          $response
     * @param string          $function
     * @param LoggerInterface $logger
     *
     * @throws \Exception
     */
    public function __construct($responseHeaders, $response, $function, $logger)
    {
        $this->response        = $response;
        $this->responseHeaders = $responseHeaders;

        if (!$response) {
            throw new \Exception("No response from PayPal.");
        }

        parse_str(urldecode($response), $this->parameters);

        $logger->debug('PayPal response to "'.$function.'".', $this->parameters);

        if (isset($this->parameters['ACK']) && ('Failure' === $this->parameters['ACK'])) {
            $this->isError = true;

            if (isset($this->parameters['L_LONGMESSAGE0'])) {
                $this->errorMessage = $this->parameters['L_LONGMESSAGE0'];
            }

            if (isset($this->parameters['PAYMENTINFO_0_SHORTMESSAGE'])) {
                $this->errorMessage = $this->parameters['PAYMENTINFO_0_SHORTMESSAGE'];
            }
        }
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     *
     * @return null
     */
    public function getResponseVar($name)
    {
        return (isset($this->parameters[$name]) ? $this->parameters[$name] : null);
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->isError;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->errorMessage;
    }

    /**
     * get debugging information
     *
     * @return array
     */
    public function debug()
    {
        return [
            'headers'            => $this->responseHeaders,
            'raw_response'       => $this->response,
            'reason'             => $this->errorMessage,
            'status'             => !$this->isError,
            'status_description' => $this->errorMessage,
            'status_is_error'    => $this->isError,
        ];
    }
}
