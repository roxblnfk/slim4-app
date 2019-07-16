<?php

namespace App\Repository;

use App\Model\Filesystem\FileInfo;

class ImageFileRepository
{
    /** @var string */
    protected $dir;
    /** @var string */
    private $baseUrl;

    public function __construct(string $dir, string $baseUrl = '/images/')
    {
        $this->dir = $dir;
        $this->baseUrl = $baseUrl;
    }
    /**
     * @param string $name
     * @return FileInfo|null
     */
    public function getImage(string $name): ?FileInfo
    {
        $name = basename($name);
        $path = $this->dir . '/' . $name;
        if (!is_file($path)) {
            return null;
        }
        $result = new FileInfo($path, $this->baseUrl . $name);
        return $result;
    }
    /**
     * @return FileInfo[]
     */
    public function getAllImages()
    {
        $files = scandir($this->dir);
        $result = [];
        foreach ($files as $file) {
            $path = $this->dir . '/' . $file;
            if ($file[0] === '.' || !is_file($path)) {
                continue;
            }
            $result[] = new FileInfo($path, $this->baseUrl . $file);
        }
        return $result;
    }
    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }
}
