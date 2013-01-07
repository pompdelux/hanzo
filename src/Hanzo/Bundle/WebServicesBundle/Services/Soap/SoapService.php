<?php

namespace Hanzo\Bundle\WebServicesBundle\Services\Soap;

use Monolog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class SoapService
{
    protected $request;
    protected $logger;
    protected $hanzo;
    protected $event_dispatcher;

    protected $timer_start;
    protected $latest_lap_time = 0;
    protected $timer_pool = [];

    public function __construct(Request $request, $logger, EventDispatcher $event_dispatcher)
    {
        $this->timer_start = $_SERVER['REQUEST_TIME_FLOAT'];

        $this->request = $request;
        $this->logger = $logger;
        $this->event_dispatcher = $event_dispatcher;

        $logger->addDebug('Soap call ... initialized.');
        $this->hanzo = Hanzo::getInstance();
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


    protected function getLapTime($lap_diff = false)
    {
        $ts = microtime(true);
        $lap = $ts - $this->timer_start;

        if ($lap_diff) {
            $return = $lap - $this->latest_lap_time;
        } else {
            $return = $lap;
        }

        $this->latest_lap_time = $lap;

        return $return;
    }

    protected function addTimestamp($label)
    {
        $ts = $this->getLapTime(true);
        $this->timer_pool[$label] = $ts;
    }

    protected function getTimerPool($as_string = false)
    {
        // we add full timer trace
        $this->timer_pool['full trace'] = microtime(true) - $this->timer_start;

        if ($as_string) {
            $string = '';
            foreach ($this->timer_pool as $key => $value) {
                $string .= ' '.$key.': '.$value."\n";
            }

            return $string;
        }

        return $this->timer_pool;
    }

    protected function logTimer($message = '', $threshold = 2)
    {
        if ((microtime(true) - $this->timer_start) > $threshold) {
            Tools::log($message."n".$this->getTimerPool(true));
        }
    }
}
