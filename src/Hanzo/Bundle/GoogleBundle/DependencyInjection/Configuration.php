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
                ->scalarNode('analytics_code')->defaultNull()->end()
                ->scalarNode('conversion_id')->defaultNull()->end()
                ->arrayNode('site_verification')
                    ->prototype('scalar')->end()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return [$v]; })
                    ->end()
                ->scalarNode('google_tag_manager_id')->defaultNull()->end()
                ->end()
                ->arrayNode('addwords')
                    ->children()
                        ->arrayNode('conversion')
                            ->children()
                                ->scalarNode('id')->defaultNull()->end()
                                ->scalarNode('language')->defaultValue('en')->end()
                                ->scalarNode('format')->defaultValue(3)->end()
                                ->scalarNode('color')->defaultValue('ffffff')->end()
                                ->scalarNode('label')->defaultNull()->end()
                                ->scalarNode('value')->defaultNull()->end()
                                ->booleanNode('remarketing_only')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
