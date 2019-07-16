<?php
namespace App\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

abstract class BaseConfiguration implements ConfigurationInterface, \ArrayAccess
{
    /** @var Processor */
    protected $processor;
    /** @var string[] */
    protected $files;
    /** @var array */
    protected $container;
    /** @var bool */
    protected $processed = false;
    /**
     * BaseConfiguration constructor.
     * @param Processor $processor
     * @param string|string[] $files
     */
    public function __construct(Processor $processor, $files)
    {
        $this->processor = $processor;
        $this->files = (array)$files;
    }
    public function __get($name)
    {
        $this->process();
        return $this->container[$name] ?? null;
    }
    public function __set($name, $value)
    {
        $this->process();
        $this->container[$name] = $value;
    }
    public function __isset($name)
    {
        $this->process();
        return isset($this->container[$name]);
    }
    public function __unset($name)
    {
        $this->process();
        if (isset($this->container[$name]))
            unset($this->container[$name]);
    }
    public function __debugInfo()
    {
        return $this->toArray();
    }

    /**
     * @param string $path
     * @param null   $default
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        $this->process();
        $keys = explode('.', $path);
        $current = &$this->container;
        foreach ($keys as $key) {
            $key = trim($key);
            if (!key_exists($key, $current))
                return $default;
            $current = &$current[$key];
        }
        return $current;
    }
    /**
     * @return array
     */
    public function toArray()
    {
        return $this->process();
    }

    /**
     * @param array $array
     * @return static
     */
    public static function fromArray(array $array)
    {
        $self = new static(new Processor(), []);
        $self->processed = true;
        $self->container = $self->processor->processConfiguration($self, [$array]);
        return $self;
    }
    /**
     * @return array
     */
    protected function process()
    {
        if ($this->processed) return $this->container;
        $parsedConfigs = $this->parseFiles();
        $this->container = $this->processor->processConfiguration($this, $parsedConfigs);
        $this->processed = true;
        return $this->container;
    }
    /**
     * @return array
     */
    protected function parseFiles()
    {
        $parsed = [];
        foreach ($this->files as $file) {
            $pos = strrpos($file, '.');
            $ext = false === $pos ? '' : substr($file, $pos + 1);
            if ($ext === 'yaml')
                $parsed[] = Yaml::parseFile($file);
        }
        return $parsed;
    }

    /**
     * @return TreeBuilder The tree builder
     */
    abstract public function getConfigTreeBuilder();

    /** @inheritDoc */
    public function offsetExists($offset)
    {
        $this->process();
        return isset($this->container[$offset]);
    }
    /** @inheritDoc */
    public function offsetGet($offset)
    {
        $this->process();
        return $this->container[$offset] ?? null;
    }
    /** @inheritDoc */
    public function offsetSet($offset, $value)
    {
        $this->process();
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    /** @inheritDoc */
    public function offsetUnset($offset)
    {
        $this->process();
        unset($this->container[$offset]);
    }
}
