<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

class MailPlatformResponseParser
{
    /**
     * The response from Guzzle
     *
     * @var Guzzle
     */
    protected $rawResponse;

    /**
     * The baserequest with method and type
     *
     * @var BaseRequest
     */
    public $originalRequest;

    /**
     * __construct
     *
     * @param Guzzle $rawResponse
     * @param BaseRequest $originalRequest
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct($rawResponse, $originalRequest)
    {
        $this->rawResponse     = $rawResponse;
        $this->originalRequest = $originalRequest;
    }

    /**
     * parse
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function parse()
    {
        $response = new MailPlatformResponse();
        $response->setStatus(BaseResponse::REQUEST_UNKNOWN_RESPONSE);

        try
        {
            $xml = $this->rawResponse->xml();
        }
        catch (\Guzzle\Common\Exception\RuntimeException $rte)
        {
            $response->setStatus(BaseResponse::REQUEST_ERROR_IN_RETURN);
            $response->setErrorMessage($rte->getMessage());
            return $response;
        }

        switch (strtolower($this->originalRequest->method))
        {
          case 'getsubscriberdetails':
              $response = $this->parseGetSubscriberDetails($xml, $response);
              break;
          case 'addsubscribertolist':
              $response = $this->parseAddSubscriberToList($xml, $response);
              break;
          case 'delete':
              $response = $this->parseDelete($xml, $response);
              break;
          case 'getlists':
              $response = $this->parseGetLists($xml, $response);
              break;
          case 'loadsubscribercustomfields':
              $response = $this->parseLoadCustomFields($xml, $response);
              break;
          default:
              error_log(__LINE__.':'.__FILE__.' Parse does not know the method'. strtolower($this->originalRequest->method)); // hf@bellcom.dk debugging
              $response->setStatus(BaseResponse::REQUEST_FAILED);
              break;
        }

        return $response;
    }

    /**
     * parseLoadCustomFields
     *
     * @param SimpleXMLElement $xml
     * @param BaseResponse $response
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function parseLoadCustomFields($xml, $response)
    {
        $responseData                    = [];
        $responseData['field_info']      = [];

        if ((string)$xml->status === 'SUCCESS' && isset($xml->data->item))
        {
            foreach ($xml->data->item as $item)
            {
                $data = [];
                $data['fieldid']   = (string)$item->fieldid;
                $data['data']      = (string)$item->data;
                $data['fieldtype'] = (string)$item->fieldtype;
                $data['fieldname'] = (string)$item->fieldname;

                $responseData['field_info'][] = $data;
            }
            $response->setData($responseData);
        }

        return $response;
    }

    /**
     * parseGetSubscriberDetails
     *
     * @param SimpleXMLElement $xml
     * @param BaseResponse $response
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function parseGetSubscriberDetails($xml, $response)
    {
        $responseData                    = [];
        $responseData['list_info']       = [];
        $responseData['subscriber_info'] = [];
        $fieldsList                      = ['subscriberid','format', 'subscribedate', 'confirmed', 'unsubscribed', 'bounced', 'disabled', 'rating', 'listid', 'listname',];
        $fieldsSubscriber                = [ 'emailaddress', ];
        $listData                        = [];
        $subscriberData                  = [];

        if ((string)$xml->data === 'Subscriber was not found')
        {
            $response->setStatus(BaseResponse::REQUEST_NOT_FOUND);
        }
        else
        {
            if ((string)$xml->status === 'SUCCESS' && isset($xml->data->item))
            {
                foreach ($xml->data->item as $lists)
                {
                    $listId = (string)$lists->listid;

                    foreach ($fieldsList as $fieldName)
                    {
                        $listData[$fieldName] = (string)$lists->{$fieldName};
                    }

                    // Subscriber is set in each list
                    foreach ($fieldsSubscriber as $fieldName)
                    {
                        $subscriberData[$fieldName] = (string)$lists->{$fieldName};
                    }

                    $responseData['list_info'][$listId] = $listData;
                    $responseData['subscriber_info'] = $subscriberData;
                }

                $response->setData($responseData);
            }
        }
        return $response;
    }

    /**
     * parseAddSubscriberToList
     *
     * @param SimpleXMLElement $xml
     * @param BaseResponse $response
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function parseAddSubscriberToList($xml, $response)
    {
        if ((string)$xml->status === 'FAILED')
        {
            // Just ignore all ready subscribed users
            $msg = 'Subscriber already exists with id:';
            if (strncmp($msg, (string)$xml->errormessage, 34) === 0)
            {
                $response->setStatus(BaseResponse::REQUEST_SUCCESS);
            }
        }
        elseif ((string)$xml->status === 'SUCCESS')
        {
            $response->setStatus(BaseResponse::REQUEST_SUCCESS);
        }

        return $response;
    }

    /**
     * parseDelete
     *
     * @param SimpleXMLElement $xml
     * @param BaseResponse $response
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function parseDelete($xml, $response)
    {
        if ((string)$xml->status === 'SUCCESS')
        {
            $response->setStatus(BaseResponse::REQUEST_SUCCESS);
        }
        else
        {
            $response->setStatus(BaseResponse::REQUEST_FAILED);
            $response->setErrorMessage((string)$xml->errormessage);
        }
        return $response;
    }

    /**
     * parseGetLists
     *
     * @param SimpleXMLElement $xml
     * @param BaseResponse $response
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function parseGetLists($xml, $response)
    {
        if ((string)$xml->status === 'SUCCESS')
        {
            if (isset($xml->data->item))
            {
                $responseData              = [];
                $responseData['list_info'] = [];
                $fieldsList                = ['listid', 'name', 'createdate', 'subscribecount', 'unsubscribecount', 'ownerid', 'username', 'fullname',];
                $listData                  = [];

                foreach ($xml->data->item as $lists)
                {
                    $listId = (string)$lists->listid;

                    foreach ($fieldsList as $fieldName)
                    {
                        $listData[$fieldName] = (string)$lists->{$fieldName};
                    }
                    $responseData['list_info'][$listId] = $listData;
                }

                $response->setData($responseData);
            }
        }
    }
}
