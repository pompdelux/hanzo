<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Bundle\ServiceBundle\Services\TwigStringService;
use Hanzo\Model\MessagesQuery;
use Hanzo\Model\MessagesI18nQuery;
use \Swift_Mailer;

class MailService
{
    protected $mailer, $twig, $settings, $swift;

    public function __construct($parameters, $settings)
    {
        if ($parameters[0] instanceof Swift_Mailer) {
            $this->mailer = $parameters[0];
        }
        else {
            throw new \InvalidArgumentException('Swift_Mailer instance required.');
        }

        if ($parameters[1] instanceof TwigStringService) {
            $this->twig = $parameters[1];
        }
        else {
            throw new \InvalidArgumentException('TwigStringService instance required.');
        }

        $this->settings = $settings;
        $this->swift = \Swift_Message::newInstance();
    }

    /**
     * Set email message body, this is done by loading templates from the messages table.
     *
     * @param string $template tis is the template identifier - excluding the .txt and/or .html postfix
     * @param mixed $parameters parameters send to the twig template
     */
    public function setMessage($template, $parameters = NULL)
    {
        $messages = MessagesI18nQuery::create()
            ->joinWithMessages()
            ->filterByLocale(Hanzo::getInstance()->get('core.locale'))
            ->useMessagesQuery()
                ->filterByNs('email')
                ->filterByKey($template.'.txt')
                ->_or()
                ->filterByKey($template.'.html')
            ->endUse()
            ->find()
        ;

        if (0 == $messages->count()) {
            throw new \InvalidArgumentException('No messages exists for the [email]: "' . $template .'" key');
        }

        // override the template loader so we can load strings from the database.
        $this->twig->startTransaction();

        foreach ($messages as $message) {
            $this->swift->setSubject($message->getSubject());

            if ('.txt' == substr($message->getMessages()->getKey(), -4)) {
                $this->swift->setBody($this->twig->parse($message->getBody(), $parameters));
            }
            elseif('.html' == substr($message->getMessages()->getKey(), -5)) {
                $this->swift->addPart($this->twig->parse($message->getBody(), $parameters), 'text/html');
            }
        }

        // reset the loader, needed to not break the reset of the application
        $this->twig->endTransaction();
    }

    /**
     * Set to address(es)
     * @see Swift_Mime_Message::setFrom
     */
    public function setTo($address)
    {
        return $this->swift->setTo($address);
    }

    /**
     * Set cc address(es)
     * @see Swift_Mime_Message::setFrom
     */
    public function setCc($address)
    {
        return $this->swift->setCc($address);
    }

    /**
     * Set bcc address(es)
     * @see Swift_Mime_Message::setFrom
     */
    public function setBcc($address)
    {
        return $this->swift->setBcc($address);
    }

    /**
     * Set from address(es)
     * @see Swift_Mime_Message::setFrom
     */
    public function setFrom($address)
    {
        $this->swift->setFrom($address);
    }


    /**
     * Send the email
     *
     * @see Swift_Transport_MailTransport::send()
     * @throws Swift_TransportException
     * @return int, number of messages send
     */
    public function send()
    {
        $hanzo = Hanzo::getInstance();
        $return_address = array($hanzo->get('email.from_email') => $hanzo->get('email.from_name'));

        $this->swift
            ->setSender($return_address)
            ->setReturnPath($hanzo->get('email.from_email'))
        ;

        if (0 == count($this->swift->getFrom())) {
            $this->setFrom($return_address);
        }

        return $this->mailer->send($this->swift);
    }
}
