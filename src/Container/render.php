<?php

use App\Config\ApplicationConfiguration;
use App\Helper\Render;

// TODO: make configurable from ApplicationConfiguration
# Render Cores
return [

    # PUG-PHP
    Render\PhugRenderer::class    => function (ApplicationConfiguration $appConf) {
        $tplDir = realpath(__DIR__ . '/../../templates/phug');
        $engine = new \Pug\Pug([
            'basedir'            => $tplDir,
            'expressionLanguage' => 'php',
            'debug'              => true,
        ]);
        $renderer = new Render\PhugRenderer($engine);
        $renderer->pagePrefix = "$tplDir/Page/";
        $renderer->layoutPrefix = "$tplDir/Layout/";
        return $renderer;
    },

    # Plates
    Render\PlatesRenderer::class  => function () {
        $tplDir = realpath(__DIR__ . '/../../templates/plates');
        $engine = new League\Plates\Engine("$tplDir/Page");
        $engine->addFolder('Page', "$tplDir/Page");
        $engine->addFolder('Layout', "$tplDir/Layout");
        $engine->addFolder('Chunk', "$tplDir/Chunk");
        $renderer = new Render\PlatesRenderer($engine);
        return $renderer;
    },

    # Twig
    Render\TwigRenderer::class    => function () {
        $tplDir = realpath(__DIR__ . '/../../templates/twig');
        $loader = new \Twig\Loader\FilesystemLoader($tplDir);
        $twig = new \Twig\Environment($loader, [
            // 'cache' => '/path/to/compilation_cache',
        ]);
        $renderer = new Render\TwigRenderer($twig);
        return $renderer;
    },

];
