<?php

namespace Hanzo\Bundle\AxBundle\Actions\In\Soap;

use Hanzo\Core\ServiceLogger;
use Monolog;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\Timer;
use Hanzo\Core\PropelReplicator;

class SoapService
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Hanzo\Core\ServiceLogger
     */
    protected $service_logger;

    /**
     * @var \Hanzo\Core\Hanzo
     */
    protected $hanzo;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $event_dispatcher;

    /**
     * @var \Hanzo\Core\PropelReplicator
     */
    protected $replicator;

    /**
     * @var \Hanzo\Core\Timer
     */
    protected $timer;

    /**
     * @var string
     */
    protected $raw_post_data;

    /**
     * @param Request          $request
     * @param LoggerInterface  $logger
     * @param ServiceLogger    $service_logger
     * @param EventDispatcher  $event_dispatcher
     * @param PropelReplicator $replicator
     */
    public function __construct(Request $request, LoggerInterface $logger, ServiceLogger $service_logger, EventDispatcher $event_dispatcher, PropelReplicator $replicator)
    {
        $this->request          = $request;
        $this->logger           = $logger;
        $this->service_logger   = $service_logger;
        $this->event_dispatcher = $event_dispatcher;
        $this->replicator       = $replicator;

        if (method_exists($this, 'boot')) {
            $this->boot();
        }
    }


    /**
     * @param $service
     * @return Response
     */
    public function exec($service)
    {
        $this->raw_post_data = file_get_contents('php://input');

        $this->timer = new Timer('soap');
        $this->hanzo = Hanzo::getInstance();
        $this->logger->addDebug('Soap call ... initialized.');

        $soap_action = explode('/', trim($this->request->server->get('HTTP_SOAPACTION'), '"'));
        $soap_action = array_pop($soap_action);

        $this->service_logger->plog($this->raw_post_data, ['incomming', 'ax', 'soap', $soap_action]);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        ob_start();
        $service->handle();
        $response->setContent(trim(ob_get_contents()));

        return $response;
    }
}
