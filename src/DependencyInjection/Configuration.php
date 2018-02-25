<?php

namespace Devhelp\PiwikBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('devhelp_piwik');

        $rootNode
            ->children()
                ->scalarNode('client')
                    ->info('service for making HTTP requests')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('api')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('url')
                                ->info('Piwik api url')
                                ->isRequired()
                            ->end()
                            ->variableNode('default_params')
                                ->info('default parameters which are going to be used in api call')
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
