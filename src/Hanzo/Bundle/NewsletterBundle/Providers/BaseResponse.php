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
     * is one of:
     *   - exists
     *   - subscribed
     *   - resubscribed
     *   - unsubscribed
     *
     * @var string
     */
    protected $action;

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $errorMessage = NULL;

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return ($this->status == self::REQUEST_SUCCESS);
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return ($this->status != self::REQUEST_SUCCESS);
    }

    /**
     * @param $code
     */
    public function setStatus($code)
    {
        $this->status = $code;
    }

    /**
     * @param $message
     */
    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * getStatus
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * getData
     * @return array
     */
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
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
