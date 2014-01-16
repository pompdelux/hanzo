<?php

namespace Hanzo\Bundle\ConsignorBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('consignor');

        $rootNode
            ->children()
                ->arrayNode('shipment_server')
                    ->children()
                        ->scalarNode('endpoint')
                            ->cannotBeEmpty()
                            ->defaultValue('https://qa-tic03.facility.dir.dk/json/ShipmentServerModule.dll')
                            ->validate()
                                ->ifTrue(function($argument) { return !filter_var($argument, FILTER_VALIDATE_URL); })
                                ->thenInvalid("Endpoint '%s' is not a valid URL")
                            ->end()
                        ->end()
                        ->arrayNode('options')
                            ->children()
                                ->scalarNode('key')->cannotBeEmpty()->defaultValue('')->end()
                                ->integerNode('actor')->cannotBeEmpty()->defaultValue(0)->end()
                                ->integerNode('product_concept_id')->defaultNull()->end()
                                ->integerNode('service_id')->defaultNull()->end()
                                ->arrayNode('to_address')
                                    ->children()
                                        ->scalarNode('name')->defaultValue('')->end()
                                        ->scalarNode('address_line_1')->defaultValue('')->end()
                                        ->scalarNode('address_line_2')->defaultValue('')->end()
                                        ->scalarNode('postal_code')->defaultValue('')->end()
                                        ->scalarNode('city')->defaultValue('')->end()
                                        ->scalarNode('country_iso2')->defaultValue('')->end()
                                        ->scalarNode('email')->defaultValue('')->end()
                                        ->scalarNode('phone')->defaultValue('')->end()
                                        ->scalarNode('attention')->defaultValue('')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ship_advisor')
                    ->children()
                        ->scalarNode('endpoint')
                            ->cannotBeEmpty()
                            ->defaultValue('http://qa-ws01.facility.dir.dk/ShipAdvisor/Main.asmx?WSDL')
                            ->validate()
                                ->ifTrue(function($argument) { return !filter_var($argument, FILTER_VALIDATE_URL); })
                                ->thenInvalid("Endpoint '%s' is not a valid URL")
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
