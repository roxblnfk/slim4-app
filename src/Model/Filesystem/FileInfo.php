<?php
namespace App\Model\Filesystem;

use ArrayAccess;

/**
 * Элемент с метаданными для списков локальных и удалённых файлов
 * @package crm\models\helper
 * @property-read $url
 * @property-read $path
 * @property-read $basename
 * @property-read $name
 * @property-read $extension
 * @property-read $size
 * @property-read $mime
 * @property-read $ctime
 * @property-read $mtime
 */
class FileInfo implements ArrayAccess
{
    protected $url;
    protected $path;
    protected $basename;
    protected $name;
    protected $extension;
    protected $size;
    protected $mime;
    protected $ctime;
    protected $mtime;
    protected $subData = [];

    public function __construct($path = null, $url = null) {
        $this->url = $url;
        if ($path) {
            $this->path = $path;
            $this->basename = basename($path);

            if (is_string($this->basename) and $r = strrchr('.', $this->basename)) {
                $this->extension = strpos($this->basename, $r + 1);
                $this->name = strpos($this->basename, 0, $r);
            } else {
                $this->name = $this->basename;
            }
            if (is_file($path)) {
                $this->size  = filesize($path);
                $this->ctime = filectime($path);
                $this->mtime = filemtime($path);
                $this->path = realpath($this->path);
            }
        }
    }
    /**
     * @param array $array
     * @return static
     */
    public static function fromArray($array = [])
    {
        $ret = new static();
        $ret->fillArray($array);
        return $ret;
    }
    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->$name ?? $this->subData[$name] ?? null;
    }
    /**
     * @param array $array
     */
    protected function fillArray($array = [])
    {
        $this->path = $array['path'] ?? $this->path;
        $this->basename = $array['basename'] ?? ($this->path ? basename($this->path) : null ) ?? $this->basename;
        $this->name = $array['name'] ?? $array['filename'] ?? $array['title'] ?? $this->name;
        $this->extension = $array['extension'] ?? $array['ext'] ?? $this->extension;
        $this->size = $array['size'] ?? $this->size;
        $this->mime = $array['mime'] ?? $array['mimetype'] ?? $this->mime;
        $this->ctime = $array['ctime'] ?? $array['timestamp'] ?? $this->ctime;
        $this->mtime = $array['mtime'] ?? $this->mtime;
        foreach ($array as $k => $v) {
            if (!property_exists($this, $k))
                $this->subData[$k] = $v;
        }
    }
    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->subData[$name] = $value;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->path;
    }

    /** @inheritDoc */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset) ?: key_exists($offset, $this->subData);
    }
    /** @inheritDoc */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
    /** @inheritDoc */
    public function offsetSet($offset, $value)
    {
        return $this->__set($offset, $value);
    }
    /** @inheritDoc */
    public function offsetUnset($offset)
    {
        if (key_exists($offset, $this->subData))
            unset($this->subData[$offset]);
    }
}
