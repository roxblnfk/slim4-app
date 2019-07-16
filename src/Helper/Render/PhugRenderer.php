<?php


namespace App\Helper\Render;


use Pug\Pug;

/**
 * @method Pug getEngine()
 */
class PhugRenderer extends BaseRenderer
{
    public $layoutPrefix = 'Layout/';
    public $pagePrefix = 'Page/';
    public $pagePostfix = '.pug';
    public $layoutPostfix = '.pug';
    /** @var Pug */
    protected $engine;

    /**
     * PlatesRenderer constructor.
     * @param Pug $engine
     */
    public function __construct(Pug $engine) {
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
        // if (!key_exists('layout', $data))
        $data['layout'] = $this->layout;
        $layoutFile = $this->layout ? $this->layoutPrefix . $this->layout . $this->layoutPostfix : null;
        $pageFile = isset($template)
            ? $this->pagePrefix . $template . $this->pagePostfix
            : $layoutFile;

        // return $this->engine->renderFile($fileName, $data);

        $add = '';
        $tpl = file_get_contents($pageFile);
        if ($layoutFile && isset($template) && substr($tpl, 0, 8) !== 'extends ') {
            $lRelFile = $this->getRelativePath($pageFile, $layoutFile);
            $add =  "extends {$lRelFile}\n";
        }
        $data = array_merge($this->layoutData, $data);
        return $this->engine->renderString($add . $tpl, $data, $pageFile);
    }

    protected function getRelativePath(string $from, string $to): string
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }
}
