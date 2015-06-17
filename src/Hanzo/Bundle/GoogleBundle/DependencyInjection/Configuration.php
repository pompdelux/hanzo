<?php

namespace Hanzo\Bundle\GoogleBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('google');

        $rootNode
            ->children()
                ->arrayNode('site_verification')
                    ->prototype('scalar')->end()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return [$v]; })
                    ->end()
                ->end()
                ->arrayNode('tag_manager')
                    ->children()
                        ->scalarNode('gtm_id')->defaultNull()->end()
                        ->arrayNode('enabled_datalayers')
                            ->useAttributeAsKey('alias', false)
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('addwords')
                            ->children()
                              ->scalarNode('conversion_id')->defaultNull()->end()
                              ->scalarNode('conversion_label')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}
