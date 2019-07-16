<?php


namespace App\Helper\Render;


use Twig\Environment;

/**
 * @method Environment getEngine()
 */
class TwigRenderer extends BaseRenderer
{
    public $layoutPrefix = 'Layout/';
    public $pagePrefix = 'Page/';
    public $pagePostfix = '.twig';
    public $layoutPostfix = '.twig';
    /** @var Environment */
    protected $engine;

    /**
     * PlatesRenderer constructor.
     * @param Environment $engine
     */
    public function __construct(Environment $engine) {
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
        // if (!key_exists('layout', $data))
        $data['layout'] = $layoutFile;
        $pageFile = isset($template)
            ? $this->pagePrefix . $template . $this->pagePostfix
            : $layoutFile;

        $data = array_merge($this->layoutData, $data);
        return $this->engine->render($pageFile, $data);
    }
}
