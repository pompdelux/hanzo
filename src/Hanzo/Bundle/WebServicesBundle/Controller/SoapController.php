<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @see
 *  http://symfony.com/doc/2.0/cookbook/web_services/php_soap_extension.html
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *
 */
class SoapController extends Controller
{
  protected $request;

  public function indexAction($version, $service_name)
  {
    $wsdl = __DIR__ . '/../Services/Soap/' . $service_name . '/' . $service_name . '.wsdl';

    if (!is_file($wsdl)) {
      throw new \Exception('Invalid or unknown SOAP service.');
    }

    $service_class = str_replace('Controller', 'Services\Soap', __NAMESPACE__) . "\\{$service_name}\\$service_name";
    $handler = new $service_class (
      $this->getRequest(),
      $this->get('Logger')
    );

    $service = new \SoapServer($wsdl);
    $service->setObject($handler);
    return $handler->exec($service);
  }
}
