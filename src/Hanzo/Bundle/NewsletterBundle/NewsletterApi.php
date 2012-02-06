<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle;

use Hanzo\Core\Hanzo
    ;

/**
 * undocumented class
 *
 * @packaged default
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class NewsletterApi
{
    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $domainKey = null;

    /**
     * __construct
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function __construct()
    {
        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');
        // FIXME: hardcoded
        $this->phplistUrl = 'http://phplist.pompdelux.dk/';
        $this->httpReferer = 'http://www.pompdelux.dk/';
    }

    /**
     * subscribe
     * @param string $email An valid e-mail
     * @param int $listid
     * @return stdClass
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function subscribe( $email, $listid  )
    {
        error_log(__LINE__.':'.__FILE__.' '.$email); // hf@bellcom.dk debugging
        $ch = curl_init( urlencode( $this->phplistUrl.'/integration/json.php?callback=PHP_'.uniqid().'&method=subscriptions:update&email='.$email.'&lists[]='.$listid.'&_='.time() ) );
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
     * @return int
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getListIdAvaliableForDomain()
    {
        $listid = 0;

        switch ($this->domainKey)
        {
            case 'DK':
                $listid = 1;
                break;
            case 'COM':
                $listid = 2;
                break;
            case 'SE':
                $listid = 4;
                break;
            case 'NO':
                $listid = 5;
                break;
            case 'NL':
                $listid = 20;
                break;
        }

        return $listid;
    }
} // END class NewsletterApi
