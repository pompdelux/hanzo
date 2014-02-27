<?php

namespace Hanzo\Bundle\AxBundle\Actions\In\Soap;

use Monolog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\Timer;
use Hanzo\Core\PropelReplicator;

class SoapService
{
    protected $request;
    protected $logger;
    protected $hanzo;
    protected $event_dispatcher;
    protected $replicator;

    protected $timer;

    public function __construct(Request $request, $logger, EventDispatcher $event_dispatcher, PropelReplicator $replicator)
    {
        $this->request          = $request;
        $this->logger           = $logger;
        $this->event_dispatcher = $event_dispatcher;
        $this->replicator       = $replicator;

        if (method_exists($this, 'boot')) {
            $this->boot();
        }
    }

    public function exec($service)
    {
        $this->timer = new Timer('soap');
        $this->hanzo = Hanzo::getInstance();
        $this->logger->addDebug('Soap call ... initialized.');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        ob_start();
        $service->handle();
        $response->setContent(trim(ob_get_contents()));

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
