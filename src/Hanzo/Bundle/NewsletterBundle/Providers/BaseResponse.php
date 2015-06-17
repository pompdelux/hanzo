<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

abstract class BaseResponse
{
    const REQUEST_SUCCESS          = 200;
    const REQUEST_UNKNOWN_RESPONSE = 400;
    const REQUEST_NOT_FOUND        = 404;
    const REQUEST_FAILED           = 500;
    const REQUEST_ERROR_IN_RETURN  = 501;

    /**
     * Is the response a success or an error, and if so, what type of error
     *
     * @var int
     */
    protected $status = NULL;

    /**
     * contains data from the response
     *
     * @var string
     */
    protected $data;

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $errorMessage = NULL;

    public function isSuccess()
    {
        return ($this->status == self::REQUEST_SUCCESS);
    }

    public function isError()
    {
        return ($this->status != self::REQUEST_SUCCESS);
    }

    public function setStatus($code)
    {
        $this->status = $code;
    }

    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * getStatus
     * @return int
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * getData
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getData()
    {
        return $this->data;
    }

    /**
     * setData
     *
     * @param array $data
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function setData($data)
    {
        $this->data = $data;
    }
}
