<?php /* vim: set sw=4: */
namespace Hanzo\Bundle\ServiceBundle;

use Hanzo\Bundle\ServiceBundle\Services;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\SettingsQuery;

class ServiceFactory
{
    protected $hanzo;

    public function __construct($environment, $container)
    {
        $this->hanzo = Hanzo::initialize($container, $environment);
    }

    public function get($service, $parameters = NULL, $class = NULL)
    {
        // figure out where to load the class from.
        if (!empty($class)) {
            $service = $class;
            $settings_key = preg_replace('/service$/', '', strtolower(basename('/'.str_replace('\\', '/', $class))));
        }
        else {
            $settings_key = strtolower($service);

            $service = ucfirst($settings_key) . 'Service';
            $service =  __NAMESPACE__ . '\\Services\\' . $service;
        }

        // return the instance
        return new $service($parameters, $this->hanzo->getByNs($settings_key));
    }
}
