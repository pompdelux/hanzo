<?php

namespace Hanzo\Bundle\LocationLocatorBundle;

use Hanzo\Core\Hanzo;

/**
 * Location locator
 *
 * @author Ulrik Nielsen <un@bellcom.dk>
 */
class LocationLocator
{
    /**
     * Translator instance
     *
     * @var object
     */
    protected $translator;

    /**
     * locator settings
     *
     * @var array
     */
    protected $settings = [];

    /**
     * service container
     *
     * @var object
     */
    protected $container;

    /**
     * __construct
     *
     * @param ServiceContainer $container
     */
    public function __construct($container)
    {
        $this->container  = $container;
        $this->logger     = $container->get('logger');
        $this->translator = $container->get('translator');
    }

    /**
     * Provider wrapper
     *
     * @param  string $method_name Name of the method
     * @param  array  $arguments   Method arguments
     * @return mixed
     */
    public function __call($method_name, array $arguments = [])
    {
        $settings = array_merge_recursive(
            $this->settings,
            Hanzo::getInstance()->getByNs('locator')
        );

        $provider = $this->container->get('hanzo_location_provider_'.$settings['provider']);

        if (!method_exists($provider, $method_name)) {
            throw new InvalidArgumentException("Method ('{$method_name}') not supported", 1);
        }

        $provider->setup($settings, $this->translator, $this->logger);
        return call_user_func_array([$provider, $method_name], $arguments);
    }
}
