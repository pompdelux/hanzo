<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

class MailPlatformResponseParser
{
    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $rawResponse;

    /**
     * undocumented class variable
     *
     * @var string
     */
    public $originalRequest;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct($rawResponse, $originalRequest)
    {
        $this->rawResponse = $rawResponse;
        $this->originalRequest = $originalRequest;
    }

    /**
     * parse
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function parse()
    {
        $response = new MailPlatformResponse();

        // TODO: what if it can't be parsed
        $xml  = $this->rawResponse->xml();

        $response->setStatus(MailPlatformResponse::REQUEST_UNKNOWN_RESPONSE);

        switch (strtolower($this->originalRequest->method))
        {
          case 'getsubscriberdetails':
            if ((string)$xml->data === 'Subscriber was not found')
            {
              $response->setStatus(MailPlatformResponse::REQUEST_NOT_FOUND);
            }
            else
            {
              if ((string)$xml->status === 'SUCCESS')
              {
                  if (isset($xml->data->item))
                  {
                      $responseData                    = [];
                      $responseData['list_info']       = [];
                      $responseData['subscriber_info'] = [];
                      $fieldsList                      = ['subscriberid','format', 'subscribedate', 'confirmed', 'unsubscribed', 'bounced', 'disabled', 'rating', 'listid', 'listname',];
                      $fieldsSubscriber                = [ 'emailaddress', ];
                      $listData                        = [];
                      $subscriberData                  = [];

                      foreach ($xml->data->item as $lists)
                      {
                          $listId = (string)$lists->listid;

                          foreach ($fieldsList as $fieldName)
                          {
                              $listData[$fieldName] = (string)$lists->{$fieldName};
                          }

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
            }
            break;
          case 'addsubscribertolist':
              if ((string)$xml->status === 'FAILED')
              {
                  // Just ignore all ready subscribed users
                  $msg = 'Subscriber already exists with id:';
                  if (strncmp($msg, (string)$xml->errormessage, 34) === 0)
                  {
                      $response->setStatus(MailPlatformResponse::REQUEST_SUCCESS);
                  }
              }
              break;
          case 'delete':
              if ((string)$xml->status === 'SUCCESS')
              {
                  $response->setStatus(MailPlatformResponse::REQUEST_SUCCESS);
              }
              else
              {
                  $response->setStatus(MailPlatformResponse::REQUEST_FAILED);
                  $response->setErrorMessage((string)$xml->errormessage);
              }
              break;
          case 'getlists':
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
              break;

          default:
              error_log(__LINE__.':'.__FILE__.' Parse does not know the method'. strtolower($this->originalRequest->method)); // hf@bellcom.dk debugging
              $response->setStatus(MailPlatformResponse::REQUEST_FAILED);
              break;
        }

        return $response;
    }
}
