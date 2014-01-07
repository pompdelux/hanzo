<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

/**
 * undocumented class
 *
 * @packaged default
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class NewsletterApi
{
    protected $domainKey       = null;
    protected $mailer          = null;
    protected $phplistUrl      = null;
    protected $httpReferer     = null;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct( $mailer )
    {
        $this->mailer = $mailer;
        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');
        // TODO: priority: low, hardcoded vars
        $this->phplistUrl = 'http://phplist.pompdelux.dk/';
        $this->httpReferer = 'http://www.pompdelux.dk/';
    }

    /**
     * sendNotificationEmail
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function sendNotificationEmail( $action, $email, $name = '' )
    {
        switch ($action)
        {
            case 'subscribe':
                $tpl = 'newsletter.subscribe';
                break;
            case 'unsubscribe':
                $tpl = 'newsletter.unsubscribe';
                break;
            default:
                return false;
                break;
        }

        $this->mailer->setMessage($tpl, array(
            'name'  => $name,
            'email' => $email,
        ));

        $this->mailer->setTo( $email, $name );
        $this->mailer->send();
        return true;
    }

    /**
     * subscribe
     * @param string $email An valid e-mail
     * @param mixed $listid int or array of int's accepted
     * @return stdClass
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function subscribe( $email, $listid  )
    {
        // Url is also hardcoded in NewsletterBundle:Default:js and in events.js

        if (!is_array($listid)) {
            $listid = [$listid];
        }

        $ids = '';
        foreach ($listid as $id) {
            $ids .= '&lists[]='.$id;
        }

        $ch = curl_init( $this->phplistUrl.'/integration/json.php?callback=PHP_'.uniqid().'&method=subscriptions:update&email='.urlencode( $email ).$ids.'&_='.time() );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $this->httpReferer);
        $result = curl_exec($ch);
        curl_close($ch);

        return $this->jsonp_decode( $result );
    }


    public function unsubscribe($email, $list_id)
    {
        if ($list_id == 'ALL') {
            $lists = $this->getAllLists($email);
            if (isset($lists->content->lists)) {
                $list_id = array_keys($lists->content->lists);
            }
        }

        if (!is_array($list_id)) {
            $list_id = [$list_id];
        }

        $ids = '';
        foreach ($list_id as $id) {
            $ids .= '&lists[]='.$id;
        }

        $ch = curl_init( $this->phplistUrl.'/integration/json.php?method=subscriptions:unsubscribe&email='.urlencode( $email ).$ids.'&_='.time() );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $this->httpReferer);
        $result = curl_exec($ch);
        curl_close($ch);

        return $this->jsonp_decode( $result );
    }


    /**
     * jsonp_decode from http://felix-kling.de/blog/2011/01/11/php-and-jsonp/
     *
     * @param string $jsonp
     * @param bool $assoc
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function jsonp_decode($jsonp, $assoc = false) {
        // PHP 5.3 adds "depth" as third parameter to json_decode
        if($jsonp[0] !== '[' && $jsonp[0] !== '{') { // we have JSONP
            $jsonp = substr($jsonp, strpos($jsonp, '('));
        }
        return json_decode(trim($jsonp,'();'), $assoc);
    }

    /**
     * getListIdAvaliableForDomain
     *
     * Contains a hardcode list of domainkey -> listid relations
     *
     * Also see ConsultantNewsletterApi
     *
     * @return int
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getListIdAvaliableForDomain()
    {
        $listid = 0;

        // TODO: priority: low, hardcoded vars
        switch ($this->domainKey)
        {
            case 'SalesDK':
            case 'DK':
                $listid = 1;
                break;
            case 'COM':
                $listid = 2;
                break;
            case 'SalesSE':
            case 'SE':
                $listid = 4;
                break;
            case 'SalesNO':
            case 'NO':
                $listid = 5;
                break;
            case 'SalesNL':
            case 'NL':
                $listid = 20;
                break;
            case 'SalesFI':
            case 'FI':
                $listid = 30;
                break;
            case 'SalesDE':
            case 'DE':
                $listid = 53;
                break;
            case 'SalesAT':
            case 'AT':
                $listid = 54;
                break;
            case 'SalesCH':
            case 'CH':
                $listid = 55;
                break;
        }

        return $listid;
    }


    public function getUserByEmail($email) {
        $wsdl = 'http://phplist.bellcom.dk/integration/phplist.pompdelux.dk/wsdl';
        $client = new \Soapclient( $wsdl );
        return $client->getUserByEmail($email);
    }

    public function getSubscriptionStateByEmail($email, $list_id)
    {
        $user = $this->getUserByEmail($email);

        if (is_array($user)) {
            return in_array($list_id, $user['subscribedLists']);
        }

        return false;
    }

    public function getAllLists($email)
    {
        $ch = curl_init( $this->phplistUrl.'/integration/json.php?'.http_build_query([
            'method' => 'lists:get',
            'email' => $email
        ]));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $this->httpReferer);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = $this->jsonp_decode($result);

        if (empty($result)) {
            $result = new \stdClass();
            $result->content = new \stdClass();
            $result->content->lists = [];
        }

        $reindexed = [];
        foreach ($result->content->lists as $index => $list) {
            if ('pdl_' == substr(strtolower($list->name), 0, 4)) {
                continue;
            }

            $reindexed[$list->id] = $list;
        }

        $result->content->lists = $reindexed;
        return $result;
    }

} // END class NewsletterApi
