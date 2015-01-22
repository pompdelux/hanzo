<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;

class MailPlatformRequest
{
    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $username;

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $token;

    /**
     * MailPlatform query
     *
     * @var string
     */
    protected $query;

    /**
     * Request type
     *
     * @var string
     */
    public $type;

    /**
     * Request method
     *
     * @var string
     */
    public $method;

    /**
     * Request body
     *
     * @var string
     */
    public $body;

    /**
     * For debugging
     *
     * @var boolean
     */
    public $dumpXML = false;

    /**
     * __construct
     *
     * @param string $username
     * @param string $token
     * @param string $baseUrl
     * @param string $query
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function __construct($username, $token, $baseUrl, $query)
    {
        $this->username = $username;
        $this->token    = $token;
        $this->query    = $query;
        $this->client   = new \Guzzle\Http\Client($baseUrl);
    }

    /**
     * buildRequest
     *
     * @return mixed XML if ok request
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function buildRequest()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><xmlrequest></xmlrequest>');
        $xml->addChild('username', $this->username);
        $xml->addChild('usertoken', $this->token);
        $xml->addChild('requesttype', $this->type);
        $xml->addChild('requestmethod', $this->method);

        $this->arrayToXML($this->body, $xml);
        return $xml->asXML();
    }

    /**
     * execute
     *
     * @return BaseResponse
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function execute()
    {
        $requestData = $this->buildRequest();
        $request = $this->client->post($this->query);
        $request->setBody($requestData);

        if ($this->dumpXML === true)
        {
            error_log(__LINE__.':'.__FILE__.':> '.PHP_EOL.$requestData); // hf@bellcom.dk debugging
        }

        $rawResponse = $request->send();

        $parser = new MailPlatformResponseParser($rawResponse, $this);
        $response = $parser->parse();

        return $response;
    }

    /**
     * arrayToXML
     * based on http://stackoverflow.com/a/5965940
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function arrayToXML($params, &$xml)
    {
        /**
         * As we can't use use the same key multiple times in an array, only the values are defined and then matched against this variable
         * So we loop over the field, e.g. 'customfields', and count how many sub elements ('item') it contains
         * For each of those sub elements a new xml child is added, and then looped over again
         *
         * Fields of type Dropdown/date are currently not supported because they require a extra sub element of key, see http://mailmailmail.net/xmlguide/index.php?rt=Subscribers&rm=Update
         */
        $multipleValueFields = ['customfields' => ['item' => ['fieldid','value']]];

        foreach($params as $key => $value)
        {
            if (isset($multipleValueFields[$key]))
            {
                $subnode = $xml->addChild($key);

                foreach ($params[$key] as $subKey => $subFields)
                {
                    $subFieldCount = count($subFields);

                    // Adds one "item" element pr. fieldid/value group
                    for ($i = 0; $i < $subFieldCount; $i++)
                    {
                        $subsubnode = $subnode->addChild($subKey);

                        // Add item[$i] elements
                        foreach ($subFields[$i] as $subsubFieldKey => $subsubField)
                        {
                            $fieldName = $multipleValueFields[$key][$subKey][$subsubFieldKey];
                            $subsubnode->addChild($fieldName,$subsubField);
                        }
                    }
                }
            }
            else
            {
                if (is_array($value))
                {
                    if (!is_numeric($key))
                    {
                        $subnode = $xml->addChild("$key");
                        $this->arrayToXML($value, $subnode);
                    }
                    else
                    {
                        $subnode = $xml->addChild("item$key");
                        $this->arrayToXML($value, $subnode);
                    }
                }
                else
                {
                    $xml->addChild("$key",htmlspecialchars("$value"));
                }
            }
        }
    }
}