<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Bundle\ServiceBundle\Services\TwigStringService;
use Hanzo\Model\MessagesQuery;
use Hanzo\Model\MessagesI18nQuery;
use \Swift_Mailer;

class MailService
{
    protected $mailer, $twig, $settings, $swift;

    /**
     * @param array $parameters
     * @param array $settings
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($parameters, $settings)
    {
        if ($parameters[0] instanceof Swift_Mailer) {
            $this->mailer = $parameters[0];
        } else {
            throw new \InvalidArgumentException('Swift_Mailer instance required.');
        }

        if ($parameters[1] instanceof TwigStringService) {
            $this->twig = $parameters[1];
        } else {
            throw new \InvalidArgumentException('TwigStringService instance required.');
        }

        $this->settings = $settings;
        $this->swift = \Swift_Message::newInstance();
    }

    /**
     * Set email message body, this is done by loading templates from the messages table.
     *
     * @param string $template   tis is the template identifier - excluding the .txt and/or .html postfix
     * @param mixed  $parameters parameters send to the twig template
     * @param string $locale     use to override default (current) locale
     * @param string $dbConn     use to override default (current) db connection
     *
     * @return MailService
     * @throws \InvalidArgumentException
     */
    public function setMessage($template, $parameters = null, $locale = null, $dbConn = null)
    {
        if (empty($locale)) {
            $locale = Hanzo::getInstance()->get('core.locale');
        }

        $messages = MessagesI18nQuery::create()
            ->joinWithMessages()
            ->filterByLocale($locale)
            ->useMessagesQuery()
                ->filterByNs('email')
                ->filterByKey($template.'.txt')
                ->_or()
                ->filterByKey($template.'.html')
            ->endUse()
            ->find($dbConn);

        if (0 == $messages->count()) {
            throw new \InvalidArgumentException('No messages exists for the [email]: "' . $template .'" key');
        }

        foreach ($messages as $message) {
            $subject = $this->twig->parse($message->getSubject(), $parameters);
            $body = $this->twig->parse($message->getBody(), $parameters);

            // skip empty messages
            if ('' == $body) {
                continue;
            }

            if ('.txt' == substr($message->getMessages()->getKey(), -4)) {
                $this->swift->addPart($body, 'text/plain');
            } elseif ('.html' == substr($message->getMessages()->getKey(), -5)) {
                $this->swift->setBody($body, 'text/html');
            }
        }

        $this->swift->setSubject($subject);

        return $this;
    }


    /**
     * set body part of an email
     *
     * @param string $body message
     * @param string $type encoding type
     *
     * @see Swift_Mime_Message::setBody
     *
     * @return MailService
     */
    public function setBody($body, $type = 'text/plain')
    {
        $this->swift->setBody($body, $type);

        return $this;
    }


    /**
     * set mail subject
     *
     * @param string $subject
     *
     * @see Swift_Mime_Message::setSubject
     *
     * @return MailService
     */
    public function setSubject($subject)
    {
        $this->swift->setSubject($subject);

        return $this;
    }


    /**
     * Set to address(es)
     *
     * @param string $address
     * @param string $name
     *
     * @see Swift_Mime_Message::setTo
     *
     * @return MailService
     */
    public function setTo($address, $name = null)
    {
        $this->swift->setTo($address, $name);

        return $this;
    }

    /**
     * Set cc address(es)
     *
     * @param string $address
     * @param string $name
     *
     * @see \Swift_Mime_Message
     *
     * @return MailService
     */
    public function setCc($address, $name = null)
    {
        $this->swift->setCc($address, $name);

        return $this;
    }

    /**
     * Set bcc address(es)
     *
     * @param string $address
     * @param string $name
     *
     * @see \Swift_Mime_Message::setFrom
     *
     * @return MailService
     */
    public function setBcc($address, $name = null)
    {
        $this->swift->setBcc($address, $name);

        return $this;
    }

    /**
     * Set from address(es)
     *
     * @param string $address
     *
     * @see Swift_Mime_Message::setFrom
     *
     * @return MailService
     */
    public function setFrom($address)
    {
        $this->swift->setFrom($address);

        return $this;
    }

    /**
     * Set Reply-To
     *
     * @param string $address
     * @param string $name
     *
     * @return MailService
     */
    public function setReplyTo($address, $name)
    {
        $this->swift->setReplyTo([$address => $name]);

        return $this;
    }

    /**
     * Set Sender
     *
     * @param string  $address
     * @param string  $name
     * @param boolean $asReturnPath
     *
     * @return MailService
     */
    public function setSender($address, $name, $asReturnPath = false)
    {
        $this->swift->setSender([$address => $name]);

        if ($asReturnPath) {
            $this->setReturnPath($address);
        }

        return $this;
    }

    /**
     * Set Return-Path
     *
     * @param string $address
     *
     * @return MailService
     */
    public function setReturnPath($address)
    {
        $this->swift->setReturnPath($address);

        return $this;
    }

    /**
     * addPart
     * @param string $message
     * @param string $mime
     *
     * @return MailService
     **/
    public function addPart($message, $mime)
    {
        $this->swift->addPart($message, $mime);

        return $this;
    }

    /**
     * Send the email
     *
     * @see Swift_Transport_MailTransport::send()
     * @throws \Swift_TransportException
     * @return int, number of messages send
     */
    public function send()
    {
        $hanzo = Hanzo::getInstance();
        $returnAddress = [$hanzo->get('email.from_email') => $hanzo->get('email.from_name')];

        if (!$this->swift->getSender()) {
            $this->swift->setSender($returnAddress);
        }

        if (!$this->swift->getReturnPath()) {
            $this->swift->setReturnPath($hanzo->get('email.from_email'));
        }

        if (0 == count($this->swift->getFrom())) {
            $this->setFrom($returnAddress);
        }

        return $this->mailer->send($this->swift);
    }

    /**
     * Attaches a file to the mail.
     *
     * @param string  $input  file path or attachment data
     * @param boolean $isFile if $input is a string, set this to false
     * @param string  $name   attachment name (if not linked file)
     *
     * @throws \InvalidArgumentException
     * @return MailService
     */
    public function addAttachment($input, $isFile = true, $name = null)
    {
        if ($isFile) {
            if (!is_file($input) || !is_readable($input)) {
                throw new \InvalidArgumentException('Attachment not readable!');
            }

            $this->swift->attach(\Swift_Attachment::fromPath($input));
        } else if ($input) {
            $this->swift->attach(\Swift_Attachment::newInstance($input, $name));
        }

        return $this;
    }
}
