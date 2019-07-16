<?php


namespace App\Helper\Render;


interface RendererInterface
{
    public function layout(string $layout, array $data = []);
    /**
     * @param string     $template
     * @param array|null $data
     * @return string
     * @throws \Throwable
     */
    public function render(?string $template, array $data = []): string;
    /**
     * @return object
     */
    public function getEngine();
}
