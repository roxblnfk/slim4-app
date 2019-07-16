<?php
namespace App\Config;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DatabaseConfiguration
 * @package App\Config
 *
 * @property bool $debug
 * @property ApplicationConfiguration[] $applications
 */
class ApplicationListConfiguration extends BaseConfiguration
{
    protected function process()
    {
        parent::process();
        $apps = [];
        foreach ($this->container['applications'] as $name => $application) {
            $apps[$name] = $application instanceof ApplicationConfiguration
                ? $application
                : ApplicationConfiguration::fromArray($application);
        }
        $this->container['applications'] = $apps;
        return $this->container;
    }

    public function determineApplication(): ApplicationConfiguration
    {
        $this->process();
        return end($this->container['applications']);
        # TODO domains comparing
        foreach ($this->container['applications'] as $application) {

        }

    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('applicationList');

        $treeBuilder
            ->getRootNode()
                ->fixXmlConfig('application', 'applications')
                ->children()
                    ->booleanNode('debug')->defaultTrue()->end()
                    ->arrayNode('applications')->isRequired()->requiresAtLeastOneElement()->useAttributeAsKey('name')
                        ->arrayPrototype()
                        ->ignoreExtraKeys(false)
                            ->fixXmlConfig('domain')
                            ->children()
                                ->arrayNode('domains')
                                    ->defaultValue([])
                                    ->beforeNormalization()
                                    ->ifString()
                                        ->then(function ($v) { return [$v]; })
                                    ->end()
                                    ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
