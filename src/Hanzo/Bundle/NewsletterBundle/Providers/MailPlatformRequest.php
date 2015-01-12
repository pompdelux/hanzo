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
     * MailPlatform base url
     *
     * @var string
     */
    protected $url;

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
    public function __construct($username, $token, $url, $client)
    {
        $this->username = $username;
        $this->token    = $token;
        $this->url      = $url;
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
        xdebug_break();
        $requestData = $this->buildRequest();
        // $request = $this->client->post('1gpjt4v1',['Content-Type' => 'text/xml; charset=UTF8']);
        $request = $this->client->post('xml.php');
        $request->setBody($requestData);
        return $request->send();
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
        foreach($params as $key => $value)
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
