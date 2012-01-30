<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use \Swift_Mailer,
    \Twig_Environment,
    \Twig_Loader_String;

use Hanzo\Model\MessagesQuery,
    Hanzo\Model\MessagesI18nQuery;

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

        if ($parameters[1] instanceof Twig_Environment) {
            $this->twig = $parameters[1];
        }
        else {
            throw new \InvalidArgumentException('TwigEngine instance required.');
        }

        if ($parameters[2] instanceof Router) {
            $this->router = $parameters[2];
        }
        else {
            throw new \InvalidArgumentException('Router instance required.');
        }

        $this->settings = $settings;
        $this->swift = \Swift_Message::newInstance();
    }


    public function setMessage($template, array $parameters = array())
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
        $loader = $this->twig->getLoader();
        $this->twig->setLoader(new Twig_Loader_String());

        foreach ($messages as $message) {
            $this->swift->setSubject($message->getSubject());

            if ('.txt' == substr($message->getMessages()->getKey(), -4)) {
                $this->swift->setBody($this->twig->render($message->getBody(), $parameters));
            }
            elseif('.html' == substr($message->getMessages()->getKey(), -5)) {
                $this->swift->addPart($this->twig->render($message->getBody(), $parameters), 'text/html');
            }
        }

        // reset the loader, needed to not break the reset of the application
        $this->twig->setLoader($loader);
    }

    public function setTo($address)
    {
        return $this->swift->setTo($address);
    }

    public function setCc($address)
    {
        return $this->swift->setCc($address);
    }

    public function setBcc($address)
    {
        return $this->swift->setBcc($address);
    }

    public function setFrom($address)
    {
        $this->swift->setFrom($address);
    }

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
