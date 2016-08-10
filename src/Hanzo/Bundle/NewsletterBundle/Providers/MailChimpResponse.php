<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

use DrewM\MailChimp\MailChimp;

/**
 * Class MailChimpResponse
 *
 * @package Hanzo\Bundle\NewsletterBundle\Providers
 */
class MailChimpResponse extends BaseResponse
{
    /**
     * MailChimpResponse constructor.
     *
     * @param MailChimp $mailChimp
     * @param array     $response
     */
    public function __construct(MailChimp $mailChimp, array $response = [])
    {
        $this->setData($response);
        $this->setStatus(BaseResponse::REQUEST_SUCCESS);

        // $mailChimp->getLastError() returns an invalid response, we do our own.
        if (isset($response['title'], $response['detail'])) {
            $this->setStatus($response['status']);
            $this->setErrorMessage($response['title'].': '.$response['detail']);
        }
    }
}