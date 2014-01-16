<?php

namespace Hanzo\Bundle\AccountBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Hanzo\Model\Addresses;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AccountExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['consignor']['installation_id'] && $config['consignor']['actor_id']) {
            $container->setParameter('account.consignor.trackntrace_url', strtr(
                $config['consignor']['trackntrace_url'], [
                    ':installation_id:' => $config['consignor']['installation_id'],
                    ':actor_id:'        => $config['consignor']['actor_id']
                ]
            ));
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
