<?php

namespace Hanzo\Bundle\NewsletterBundle\Providers;
use DrewM\MailChimp\MailChimp;

/**
 * Class MailChimpProvider
 *
 * @package Hanzo\Bundle\NewsletterBundle\Providers
 */
class MailChimpProvider extends BaseProvider
{
    /**
     * @var string
     */
    private $apiKey = '598d67b9be624a98c3fb396e473f2c8c-us13';

    /**
     * Domain to list id mapping
     *
     * @var array
     */
    protected $domainToListMap = [
        'com' => '4f3e01996e',
        'dk'  => 'ccf5b8a169',
        'se'  => '5d4cc4f5cd',
        'no'  => '9b0cbc4d0f',
        'nl'  => '34e0e1a085',
        'fi'  => '5f96c5e7da',
        'de'  => '85697fe203',
        'ch'  => 'df4aecf940',
        'at'  => '443d86de4a',
    ];

    /**
     * Language mapping
     *
     * @var array
     */
    protected $domainToLanguageMap = [
        'dk'  => 'da',
        'com' => 'en',
        'nl'  => 'en',
        'fi'  => 'en',
        'ch'  => 'en',
        'se'  => 'se',
        'no'  => 'no',
        'de'  => 'de',
        'at'  => 'de',
    ];

    /**
     * @var string
     */
    private $domainKey;
    
    /**
     * MailChimpProvider constructor.
     *
     * @param string $domainKey
     */
    public function __construct($domainKey)
    {
        $this->domainKey = str_replace('sales', '', strtolower($domainKey));
    }

    /**
     * @param string $subscriber_id
     * @param string $list_id
     * @param array  $params
     * @return MailChimpResponse
     */
    public function subscriberCreate($subscriber_id, $list_id, array $params = [])
    {
        // If subscriber is unsubscribed (not deleted) we have to activate the subscriber first - else just create
        $response = $this->subscriberActivate($subscriber_id, $list_id);

        if ($response->getStatus() === BaseResponse::REQUEST_SUCCESS) {
            return $response;
        } else {
            return $this->subscriberAddToList($subscriber_id, $list_id, $params);
        }
    }

    /**
     * Unsubscribe a list member
     *
     * @param string $subscriber_id
     * @param int    $list_id
     * @param array  $params
     *
     * @return MailChimpResponse
     */
    public function subscriberDelete($subscriber_id, $list_id, array $params = [])
    {
        $client = $this->getMailChimpClient();

        return $client->patch("lists/{$list_id}/members/".$client->subscriberHash($subscriber_id), [
            'status' => 'unsubscribed',
        ]);
    }

    /**
     * Activate a known list member
     *
     * @param string $subscriber_id
     * @param int    $list_id
     *
     * @return MailChimpResponse
     */
    public function subscriberActivate($subscriber_id, $list_id)
    {
        $client = $this->getMailChimpClient();
        $response = $client->get("lists/{$list_id}/members/".$client->subscriberHash($subscriber_id));

        if (($response->getStatus() === BaseResponse::REQUEST_SUCCESS) &&
            ('subscribed' != $response->getData()['status'])
        ) {
            $response = $client->patch("lists/{$list_id}/members/".$client->subscriberHash($subscriber_id), [
                'status' => 'subscribed',
            ]);
        }

        return $response;
    }

    /**
     * Subscribe a new member to a list
     *
     * @param string $subscriber_id
     * @param string $list_id
     * @param array  $params
     *
     * @return MailChimpResponse
     */
    public function subscriberAddToList($subscriber_id, $list_id, array $params = [])
    {
        $data = [
            'email_address' => $subscriber_id,
            'status'        => 'subscribed',
            'merge_fields'  => [
                'FNAME' => $params['name'],
            ],
        ];

        if (isset($this->domainToLanguageMap[$this->domainKey])) {
            $data['language'] = $this->domainToLanguageMap[$this->domainKey];
        }

        $client = $this->getMailChimpClient();

        return $client->post("lists/{$list_id}/members", $data);
    }

    /**
     * @param array $params
     *
     * @return MailChimpResponse
     */
    public function listsGet(array $params = [])
    {
        $lists = [];
        foreach ($this->domainToListMap as $country => $id) {
            $lists[] = [
                'listid'     => $id,
                'name'       => $country,
                'createdate' => '1466853412',
                'ownerid'    => '',
            ];
        }

        $response = new MailChimpResponse(new MailChimp($this->apiKey));
        $response->setData(['list_info' => $lists]);

        return $response;
    }

    // Not used any more.
    public function subscriberGet($subscriber_id){}
    public function subscriberUpdate($subscriber_id, $list_id, array $params = []){}
    public function subscriberIsSubscribed($subscriber_id, array $list_ids){}
    public function subscriberGetSubscribedLists($subscriber_id){}

    /**
     * @return MailChimpRequest
     */
    private function getMailChimpClient()
    {
        return new MailChimpRequest($this->apiKey);
    }
}