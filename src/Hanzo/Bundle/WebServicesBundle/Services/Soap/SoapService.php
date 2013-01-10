<?php

namespace Hanzo\Bundle\WebServicesBundle\Services\Soap;

use Monolog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\Timer;

class SoapService
{
    protected $request;
    protected $logger;
    protected $hanzo;
    protected $event_dispatcher;

    protected $timer;

    public function __construct(Request $request, $logger, EventDispatcher $event_dispatcher)
    {
        $this->timer = new Timer('soap');

        $this->request = $request;
        $this->logger = $logger;
        $this->event_dispatcher = $event_dispatcher;

        $logger->addDebug('Soap call ... initialized.');
        $this->hanzo = Hanzo::getInstance();

        if (method_exists($this, 'boot')) {
            $this->boot();
        }
    }

    public function exec($service)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        ob_start();
        $service->handle();
        $var = ob_get_contents();
        $response->setContent(trim($var));

        return $response;
    }

    public static function getDebugInfo()
    {
        return array(
            'xml' => file_get_contents('php://input'),
            'env' => $_SERVER
        );
    }
}
