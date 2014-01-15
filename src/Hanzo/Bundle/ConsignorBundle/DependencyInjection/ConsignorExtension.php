<?php

namespace Hanzo\Bundle\ConsignorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ConsignorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('consignor.shipment_server.endpoint', $config['shipment_server']['endpoint']);
        $container->setParameter('consignor.ship_advisor.endpoint', $config['ship_advisor']['endpoint']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['shipment_server']['options']) && count($config['shipment_server']['options'])) {

            $def = $container->getDefinition('consignor.service');
            $def->addmethodcall('setOptions', [$config['shipment_server']['options']]);
        }
    }
}
