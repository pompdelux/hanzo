<?php

namespace Hanzo\Bundle\AccountBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('account');

        $rootNode
            ->children()
                ->arrayNode('consignor')
                    ->isRequired()
                    ->children()
                        ->scalarNode('trackntrace_url')
                            ->defaultValue('http://myconsignor.no/WebShopPackageTracker.aspx?installationID=:installation_id:&ActorID=:actor_id:&orderNUmber=:order_id:')
                        ->end()
                        ->scalarNode('installation_id')
                            ->defaultValue('')
                        ->end()
                        ->scalarNode('actor_id')
                            ->defaultValue('')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;


        /*


        installationID
        90290000026

        ActorID
        POMPdeLUX DK 63
        POMPdeLUX NO 66
        POMPdeLUX SE 64
        POMPdeLUX NL 67
        POMPdeLUX FI 68
        POMPdeLUX DE 69

        */

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
