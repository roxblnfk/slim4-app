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
 * @property string $directory
 * @property string $table
 * @property bool $safe
 * @property string[] $entities
 */
class MigrationConfiguration extends BaseConfiguration
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('migration');

        $treeBuilder
            ->getRootNode()
                ->fixXmlConfig('entity', 'entities')
                ->children()
                    ->scalarNode('directory')->cannotBeEmpty()->defaultValue('src/Console/Migration')->end()
                    ->arrayNode('entities')
                        ->defaultValue(['src/Entity'])
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function ($v) { return [$v]; })
                        ->end()
                        ->scalarPrototype()->end()
                    ->end()
                    ->scalarNode('table')->cannotBeEmpty()->defaultValue('migration')->end()
                    ->booleanNode('safe')->defaultFalse()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
