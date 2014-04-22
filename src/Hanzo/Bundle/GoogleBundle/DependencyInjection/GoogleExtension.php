<?php

namespace Hanzo\Bundle\GoogleBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class GoogleExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!is_array($config['site_verification'])) {
            $config['site_verification'] = [$config['site_verification']];
        }

        $container->setParameter('google.analytics_code', $config['analytics_code']);
        $container->setParameter('google.conversion_id', $config['conversion_id']);
        $container->setParameter('google.site_verification', $config['site_verification']);

        $addwords_params = [
            'id'               => '',
            'language'         => 'en',
            'format'           => '3',
            'color'            => 'ffffff',
            'label'            => '',
            'value'            => '',
            'remarketing_only' => false,
        ];

        foreach ($addwords_params as $key => $v) {
            if (isset($config['addwords']) && $config['addwords']['conversion'][$key]) {
                $addwords_params[$key] = $config['addwords']['conversion'][$key];
            }
        }

        $container->setParameter('google.addwords.conversion.id', $addwords_params['id']);
        $container->setParameter('google.addwords.conversion.language', $addwords_params['language']);
        $container->setParameter('google.addwords.conversion.format', $addwords_params['format']);
        $container->setParameter('google.addwords.conversion.color', $addwords_params['color']);
        $container->setParameter('google.addwords.conversion.label', $addwords_params['label']);
        $container->setParameter('google.addwords.conversion.value', $addwords_params['value']);
        $container->setParameter('google.addwords.conversion.remarketing_only', $addwords_params['remarketing_only']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
