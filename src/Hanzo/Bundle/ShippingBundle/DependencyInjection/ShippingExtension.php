<?php

namespace Hanzo\Bundle\ShippingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ShippingExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $validation_files = $container->getParameter('validator.mapping.loader.yaml_files_loader.mapping_files');

        $file = realpath(__DIR__.'/../Resources/config/validation.'.$container->getParameterBag()->get('locale').'.yml');

        if (file_exists($file)) {
            // $remove = realpath(__DIR__.'/../Resources/config/validation.yml');
            // $k = array_flip($validation_files);
            // unset($k[$remove]);
            // $validation_files = array_flip($k);
            $validation_files[] = $file;
        }

        $container->setParameter('validator.mapping.loader.yaml_files_loader.mapping_files', $validation_files);

        \Hanzo\Core\Tools::log($container->getParameter('validator.mapping.loader.yaml_files_loader.mapping_files'));
    }
}
