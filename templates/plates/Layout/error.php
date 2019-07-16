<?
/**
 * @var League\Plates\Template\Template $this
 * @var \Slim\Exception\HttpException $error
 */
?><html>
    <head>
        <meta charset="utf-8">
        <title><?= $this->e($title ?? $error->getMessage()) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/uikit/3.1.6/css/uikit.min.css" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/uikit/3.1.6/js/uikit.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/uikit/3.1.6/js/uikit-icons.min.js"></script>
    </head>
    <body>
        Error <?= $this->e($error->getCode()) ?>
        <br> <?= $this->e($error->getMessage()) ?>
        <?= $this->section('content') ?>
        <?// d($error) ?>
    </body>
</html>
