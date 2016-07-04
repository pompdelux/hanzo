<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

use DrewM\MailChimp\MailChimp;
use DrewM\MailChimp\Batch;

/**
 * Class MailChimpRequest
 *
 * @method string subscriberHash($email) Creates a MailChimp hash of the email.
 * @method Batch new_batch() Creates a new Batch object.
 * @method bool success() Was the last request successful?
 * @method array|false getLastError() Get the last error returned by either the network transport, or by the API.
 * @method array getLastResponse() Get an array containing the HTTP headers and the body of the API response.
 * @method array getLastRequest() Get an array containing the HTTP headers and the body of the API request.
 * @method MailChimpResponse delete($method, $args = array(), $timeout = 10) Make an HTTP DELETE request - for deleting data.
 * @method MailChimpResponse get($method, $args = array(), $timeout = 10) Make an HTTP GET request - for retrieving data.
 * @method MailChimpResponse patch($method, $args = array(), $timeout = 10) Make an HTTP PATCH request - for performing partial updates.
 * @method MailChimpResponse post($method, $args = array(), $timeout = 10) Make an HTTP POST request - for creating and updating items.
 * @method MailChimpResponse put($method, $args = array(), $timeout = 10) Make an HTTP PUT request - for creating new items.
 *
 * @package Hanzo\Bundle\NewsletterBundle\Providers
 */
class MailChimpRequest
{
    /**
     * @var MailChimp
     */
    private $mailChimp;

    /**
     * MailChimpRequest constructor.
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->mailChimp = new MailChimp($apiKey);
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return MailChimpResponse|mixed
     */
    public function __call($methodName, array $arguments = [])
    {
        if (!method_exists($this->mailChimp, $methodName)) {
            throw new \InvalidArgumentException("{$methodName} is not a known method.");
        }

        $passthrough = [
            'getLastError',
            'getLastResponse',
            'getLastRequest',
            'new_batch',
            'subscriberHash',
            'success',
        ];

        $response = call_user_func_array([$this->mailChimp, $methodName], $arguments);

        if (in_array($methodName, $passthrough)) {
            return $response;
        }
        
        return new MailChimpResponse($this->mailChimp, $response);
    }
}