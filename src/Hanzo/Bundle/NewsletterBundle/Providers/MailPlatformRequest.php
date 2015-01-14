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
     * __construct
     *
     * @param string $username
     * @param string $token
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function __construct($username, $token, $query, $client)
    {
        $this->username = $username;
        $this->token    = $token;
        $this->query    = $query;
        $this->client   = $client;
    }

    /**
     * buildRequest
     *
     *
     * @return void
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

    public function execute()
    {
        $requestData = $this->buildRequest();
        $request = $this->client->post($this->query);
        $request->setBody($requestData);
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
         */
        $multipleValueFields = ['customfields' => ['item' => ['fieldid','value']]];

        foreach($params as $key => $value)
        {
            if (isset($multipleValueFields[$key]))
            {
                $subnode = $xml->addChild($key);

                foreach ($params[$key] as $subKey => $subFields)
                {
                    $subFieldCount = count($params[$key][$subKey]);

                    for ($i = 0; $i < $subFieldCount; $i++)
                    {
                        $subsubnode = $subnode->addChild($subKey);

                        foreach ($subFields as $index => $subsubField)
                        {
                            $fieldName = $multipleValueFields[$key][$subKey][$index];

                            $subsubnode->addChild($fieldName,$subsubField[$index]);
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
