<?php

use App\Repository\ImageFileRepository;

return [

    ImageFileRepository::class => DI\create(ImageFileRepository::class)
        ->constructor(App\Conf::$conf['imgPath'], App\Conf::$conf['imgBaseUrl']),

];
