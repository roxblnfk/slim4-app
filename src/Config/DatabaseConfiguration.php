<?php
namespace App\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DatabaseConfiguration
 * @package App\Config
 *
 * @property $default
 * @property $databases
 * @property $connections
 */
class DatabaseConfiguration extends BaseConfiguration
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('database');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('default')->cannotBeEmpty()->end()
                    ->arrayNode('databases')->isRequired()->requiresAtLeastOneElement()->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('connection')->isRequired()->end()
                                ->scalarNode('readConnection')->end()
                                ->scalarNode('prefix')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('connections')->isRequired()->requiresAtLeastOneElement()->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('connection')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('username')->end()
                                ->scalarNode('password')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
