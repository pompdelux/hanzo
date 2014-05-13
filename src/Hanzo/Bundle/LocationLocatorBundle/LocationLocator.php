<?php

namespace Hanzo\Bundle\LocationLocatorBundle;

use InvalidArgumentException;

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
     * @param string           $environment
     */
    public function __construct($container, $environment)
    {
        $this->container      = $container;
        $this->logger         = $container->get('logger');
        $this->service_logger = $container->get('hanzo.external_service.logger');
        $this->translator     = $container->get('translator');
        $this->environment    = $environment;
    }

    /**
     * Provider wrapper
     *
     * @param  string $method_name      Name of the method
     * @param  array  $arguments        Method arguments
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return mixed
     */
    public function __call($method_name, array $arguments = [])
    {
        // TODO: better implementation of settings - do not use Hanzo here!
        $settings = array_merge_recursive(
            $this->settings,
            Hanzo::getInstance()->getByNs('locator')
        );

        try {
            $provider = $this->container->get('hanzo_location_provider_'.$settings['provider']);
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Service "hanzo_location_provider_'.$settings['provider'].'" not found');
        }

        if (!method_exists($provider, $method_name)) {
            throw new InvalidArgumentException('Method ('.$method_name.') not supported');
        }

        $provider->setup($settings, $this->translator, $this->logger, $this->service_logger, $this->environment);
        return call_user_func_array([$provider, $method_name], $arguments);
    }
}
