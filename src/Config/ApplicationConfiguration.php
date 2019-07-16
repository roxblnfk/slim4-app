<?php
namespace App\Config;

use mysql_xdevapi\Exception;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DatabaseConfiguration
 * @package App\Config
 *
 *
 * @property string[] $domains
 * @property string[] $containers
 * @property string[] $routes
 * @property string[] $middlewares
 * @property string[] $templates
 * @property string   $cache
 */
class ApplicationConfiguration extends BaseConfiguration
{
    /** @inheritDoc */
    public function getConfigTreeBuilder()
    {
        /**
         * @param       $name
         * @param array $defaultValue
         * @return NodeDefinition
         */
        $listNode = function ($name, $defaultValue = []) {
            $treeBuilder = new TreeBuilder($name);
            return $treeBuilder->getRootNode()
                ->defaultValue($defaultValue)
                ->beforeNormalization()
                ->ifString()
                ->then(function ($v) { return [$v]; })
                    ->end()
                ->scalarPrototype()->end();
        };

        $treeBuilder = new TreeBuilder('application');
        return $treeBuilder
            ->getRootNode()
                ->ignoreExtraKeys(false)
                ->fixXmlConfig('domain')
                ->fixXmlConfig('container')
                ->fixXmlConfig('route')
                ->fixXmlConfig('middleware')
                ->fixXmlConfig('template')
                ->children()
                    ->append($listNode('domains'))
                    ->append($listNode('containers'))
                    ->append($listNode('routes'))
                    ->append($listNode('middlewares'))
                    ->append($listNode('templates'))
                    ->scalarNode('cache')->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param $fromPath
     * @return array
     * @throws \Exception
     */
    public function getContainerDefinitions($fromPath): array
    {
        $defs = [static::class => $this];
        foreach ($this->container['containers'] as $container) {
            $file = $fromPath . DIRECTORY_SEPARATOR . $container . '.php';
            if (!is_file($file))
                throw new \Exception('File Not Found');
            $defs += include $file;
        }
        return $defs;
    }
}
