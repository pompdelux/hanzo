<?php /* vim: set sw=4: */
namespace Hanzo\Bundle\ServiceBundle;

use Hanzo\Core\Hanzo;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ServiceFactory
 *
 * @package Hanzo\Bundle\ServiceBundle
 */
class ServiceFactory
{
    protected $hanzo;

    /**
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->hanzo = Hanzo::initialize($container);
    }

    /**
     * @param string $service
     * @param array  $parameters
     * @param string $class
     *
     * @return mixed
     */
    public function get($service, $parameters = null, $class = null)
    {
        // figure out where to load the class from.
        if (!empty($class)) {
            $service = $class;
            $settingsKey = preg_replace('/service$/', '', strtolower(basename('/'.str_replace('\\', '/', $class))));
        } else {
            $settingsKey = strtolower($service);

            $service = ucfirst($settingsKey) . 'Service';
            $service =  __NAMESPACE__ . '\\Services\\' . $service;
        }
        // return the instance
        return new $service($parameters, $this->hanzo->getByNs($settingsKey));
    }
}
