<?php

namespace Hanzo\Bundle\PaymentBundle\Methods\PayPal;

class PayPalCallResponse
{
    protected $is_error = false;
    protected $parameters = [];

    protected $response;
    protected $response_headers;
    protected $error_message;

    public function __construct($response_headers, $response, $function)
    {
        $this->response = $response;
        $this->response_headers = $response_headers;

        if (!$response) {
            throw new Exception("No response from PayPal.");
        }

        parse_str(urldecode($response), $this->parameters);
\Hanzo\Core\Tools::log($this->parameters);
        if (isset($this->parameters['ACK']) && ('Failure' === $this->parameters['ACK'])) {
            $this->is_error = true;

            if (isset($this->parameters['L_LONGMESSAGE0'])) {
                $this->error_message = $this->parameters['L_LONGMESSAGE0'];
            }

            if (isset($this->parameters['PAYMENTINFO_0_SHORTMESSAGE'])) {
                $this->error_message = $this->parameters['PAYMENTINFO_0_SHORTMESSAGE'];
            }
        }
    }

    public function getResponse()
    {
        return $this->parameters;
    }

    public function getResponseVar($name)
    {
        return (isset($this->parameters[$name]) ? $this->parameters[$name] : null);
    }


    public function isError()
    {
        return $this->is_error;
    }

    public function getError()
    {
        return $this->error_message;
    }

    /**
     * get debugging information
     *
     * @return array
     */
    public function debug()
    {
        return [
            'headers' => $this->response_headers,
            'raw_response' => $this->response,
            'reason' => $this->error_message,
            'status' => !$this->is_error,
            'status_description' => $this->error_message,
            'status_is_error' => $this->is_error,
        ];
    }
}
