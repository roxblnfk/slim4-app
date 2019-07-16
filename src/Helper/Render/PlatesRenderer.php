<?php


namespace App\Helper\Render;


use League\Plates\Engine;

/**
 * @method Engine getEngine()
 */
class PlatesRenderer extends BaseRenderer
{
    public $layoutPrefix = 'Layout::';
    public $pagePrefix = 'Page::';
    public $pagePostfix = '';
    public $layoutPostfix = '';

    /** @var Engine */
    protected $engine;

    /**
     * PlatesRenderer constructor.
     * @param Engine $engine
     */
    public function __construct(Engine $engine) {
        $this->engine = $engine;
    }

    /**
     * @param string|null $template
     * @param array       $data
     * @return string
     * @throws \Throwable
     */
    public function render(?string $template, array $data = []): string
    {
        $layoutFile = $this->layout ? $this->layoutPrefix . $this->layout . $this->layoutPostfix : null;
        $pageFile = isset($template)
            ? $this->pagePrefix . $template . $this->pagePostfix
            : $layoutFile;

        $tpl = $this->engine->make($pageFile);
        if ($layoutFile && isset($template)) {
            $tpl->layout($layoutFile, $this->layoutData);
        } elseif (!$template) {
            $data = array_merge($this->layoutData, $data);
        }
        return $tpl->render($data);
    }
}
