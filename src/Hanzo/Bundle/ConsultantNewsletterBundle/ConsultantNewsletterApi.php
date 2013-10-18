<?php

namespace Hanzo\Bundle\ConsultantNewsletterBundle;

use Hanzo\Core\Hanzo;

/**
 * undocumented class
 *
 * @packaged default
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class ConsultantNewsletterApi
{
    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $domainKey = null;

    protected $soapClient = null;


    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted'; // aka ready for sending

    public function __construct( $wsdl = '' )
    {
        $this->domainKey = Hanzo::getInstance()->get('core.domain_key');

        try {
            if (empty($wsdl)) {
                $wsdl = 'http://phplist.bellcom.dk/integration/phplist.konsulent.pompdelux.dk/wsdl';
            }

            $this->soapClient = new \Soapclient( $wsdl, ['trace' => true] );

        } catch (Exception $e) {
            throw new Exception( "Could not create soap client: ". $e->getMessage() );
        }
    }

    public function getUserByEmail($email_address)
    {
        return $this->soapClient->getUserByEmail($email_address);
    }

    public function getUserById($userId)
    {
        return $this->soapClient->getUserById($userId);
    }

    public function subscribeUser(array $customerData, array $listIds, $autoConfirm = TRUE)
    {
        $user = $this->getUserByEmail( $customerData['email_address'] );

        $old_list        = array();
        $dublicate_lists = array();
        $new_lists       = array();

        if ($user === TRUE) {
            $old_list = $this->soapClient->getSubscribedListsForUserWithEmail( $customerData['email_address'] );

            foreach ($listIds as $listId) {
                if (!empty($old_list) && in_array($listId, $old_list)) {
                    $dublicate_lists[] = $listId;
                } else {
                    $new_lists[] = $listId;
                }
            }
        } else {
            $new_lists = $listIds; // If the user is new just subscribe to all lists
        }

        if (!empty($dublicate_lists) && empty($new_lists)) {
            return false;
        }

        //subscribe the new user to the correct lists
        $firstName = (isset($customerData['firstname']) ? $customerData['firstname'] : '');
        $lastName = (isset($customerData['lastname']) ? $customerData['lastname'] : '');
        foreach ($new_lists as $listId) {
            try {
                $result = $this->soapClient->subscribeUser( $firstName, $lastName, $customerData['email_address'] ,array( $listId ), $customerData['attributes'] );
            } catch (\SoapFault $e){
                return $e->getMessage();
            }
        }

        return $result;
    }

    public function unSubscribeUser($userId, $listId)
    {
        $user = $this->getUserById($userId);
        $this->soapClient->unSubscribeUser( $user['email'], array($listId));
    }

    public function getActiveLists()
    {
        try {
            return $this->soapClient->getActiveLists();
        } catch(Exception $e) {
            return false;
        }
    }

    public function getSubscribedLists($userId)
    {
        $user = $this->getUserById( $userId );
        return $this->getSubscribedListsForUserWithEmail($user['email']);
    }

    public function confirmUserByUniqId($uniqid)
    {
        $this->soapClient->confirmUserByUniqId($uniqid);
    }

    public function assignUserToList($userId, $listId)
    {
        $this->soapClient->assignUserToList( $userId, $listId );
    }

    public function scheduleNewsletter($from, $to, $replyto, $subject, $message, $footer = null, array $lists, $template, $status = self::STATUS_DRAFT, $embargo = null, $userselection = null, $owner = null)
    {
        $request = (object) array(
            'fromfield'     => $from,
            'tofield'       => $to,
            'replyto'       => $replyto,
            'subject'       => $subject,
            'message'       => $message,
            'footer'        => $footer,
            'lists'         => $lists,
            'template'      => $template,
            'status'        => $status,
            'embargo'       => (is_null($embargo) ? date('Y-m-d H:i:s') : $embargo),
            'userselection' => $userselection,
            'owner'         => $owner,
        );

        try {
            return $this->soapClient->scheduleNewsletter( $request );
        } catch (Exception $e) {
            error_log(__LINE__.':'.__FILE__.' '.$e->getMessage());
        }
    }

    public function sendTestMail($from, $to, $replyto, $subject, $message, $footer = null, array $lists, $template, $status = self::STATUS_DRAFT, $testReciverEmail)
    {
        $request = (object) array(
            'fromfield'     => $from,
            'tofield'       => $to,
            'replyto'       => $replyto,
            'subject'       => $subject,
            'message'       => $message,
            'footer'        => $footer,
            'lists'         => $lists,
            'template'      => $template,
            'status'        => $status,
        );

        try {
            return $this->soapClient->sendTestMail( $request, $testReciverEmail );
        } catch (Exception $e) {
            error_log(__LINE__.':'.__FILE__.' '.$e->getMessage());
            return false;
        }
    }

    public function getTemplates()
    {
        try {
            return $this->soapClient->getTemplates();
        } catch (Exception $e) {
            error_log(__LINE__.':'.__FILE__.' '.$e->getMessage());
        }
    }


    /**
    * Checks if an admin users exists
    *
    * @param string $email
    * @return bool
    * @author Henrik Farre <hf@bellcom.dk>
    **/
    public function doesAdminUserExist($email)
    {
        $x = $this->soapClient->doesAdminUserExist($email);

error_log($this->soapClient->__getLastRequestHeaders());
error_log($this->soapClient->__getLastRequest());


        return $x;
    }

    /**
    * Creates an admin user in phplist
    *
    * @param stdClass $user
    * @param stdClass $access
    * @return bool
    * @author Henrik Farre <hf@bellcom.dk>
    **/
    public function addAdminUser(\stdClass $user, \stdClass $access)
    {
        return $this->soapClient->addAdminUser($user, $access);
    }

    /**
    * Get an admin user
    *
    * @param string $email
    * @return stdClass
    * @author Henrik Farre <hf@bellcom.dk>
    **/
    public function getAdminUserByEmail($email)
    {
        return (object) $this->soapClient->getAdminUserByEmail($email);
    }


    /**
    * Creates a list
    *
    * @param stdClass $list
    * @return void
    * @author Henrik Farre <hf@bellcom.dk>
    **/
    public function createList(\stdClass $list)
    {
        $this->soapClient->createList($list);
    }

    /**
    * Gets all lists owned by an admin
    *
    * @param int $ownerID
    * @return array
    * @author Henrik Farre <hf@bellcom.dk>
    **/
    public function getListsByOwner($ownerID)
    {
        return $this->soapClient->getListsByOwner($ownerID);
    }

    /**
    * Returns all users subscribed to a list
    *
    * @param int $listID
    * @param bool $confirmed If false unconfirmed users will also be returned
    * @return array
    * @author Henrik Farre <hf@bellcom.dk>
    **/
    public function getAllUsersSubscribedToList($listID, $confirmed = true)
    {
        return $this->soapClient->getAllUsersSubscribedToList($listID, $confirmed);
    }

    /**
    * getNewsletterHistory
    * @return array
    * @author Henrik Farre <hf@bellcom.dk>
    **/
    public function getNewsletterHistory($userID)
    {
        return $this->soapClient->getNewsletterHistory($userID);
    }

    /**
     * sendNotificationEmail
     * @return void
     * @author Henrik Farre <hf@bellcom.dk> / Anders Bryrup
     **/
    public function sendNotificationEmail($mailer, $tpl, $email, $name = '')
    {
        $mailer->setMessage($tpl, array(
            'name'  => $name,
            'email' => $email,
        ));

        $mailer->setTo($email, $name);
        $mailer->send();
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
     * Also see NewsletterApi
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
            case 'FI':
                $listid = 30;
                break;
        }

        return $listid;
    }
} // END class ConsultantNewsletterApi
