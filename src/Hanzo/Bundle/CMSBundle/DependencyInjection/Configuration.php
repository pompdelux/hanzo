<?php

namespace Hanzo\Bundle\CMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hanzo_cms');

        $rootNode
            ->children()
                ->arrayNode('twig')
                    ->children()
                        ->arrayNode('left_menu')
                            ->children()
                                ->scalarNode('type')->defaultValue('main')->end()
                                ->scalarNode('from')->defaultValue(20)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('sub_menu')
                            ->children()
                                ->scalarNode('type')->defaultValue('sub')->end()
                                ->scalarNode('from')->defaultValue(400)->end()
                                ->scalarNode('offset')->defaultValue(482)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
