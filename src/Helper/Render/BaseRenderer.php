<?php

namespace App\Helper\Render;

abstract class BaseRenderer implements RendererInterface
{
    protected $engine;
    protected $layout;
    protected $layoutData;

    abstract public function render(?string $template, array $data = []): string;

    public function layout(string $layout, array $data = [])
    {
        $this->layout = $layout;
        $this->layoutData = $data;
    }

    public function getEngine()
    {
        return $this->engine;
    }
}
