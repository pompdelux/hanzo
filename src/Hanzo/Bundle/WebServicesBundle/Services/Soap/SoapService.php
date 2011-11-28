<?php

namespace Hanzo\Bundle\WebServicesBundle\Services\Soap;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog;

class SoapService
{
  protected $request;
  protected $logger;

  public function __construct(Request $request, $logger)
  {
    $this->request  = $request;
    $this->logger   = $logger;
    $logger->addDebug('Soap call ... initialized.');
  }

  public function exec($service)
  {
      $response = new Response();
      $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

      ob_start();
      $service->handle();
      $var = ob_get_contents();
      ob_end_clean();
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
