<?php

use App\Config\MigrationConfiguration;
use Spiral\Database;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\FileRepository;
use Spiral\Migrations\Migrator;

return [

    MigrationConfiguration::class => DI\autowire(MigrationConfiguration::class)
        ->constructorParameter('files', ['config/migration.yaml']),

    Migrator::class => function (MigrationConfiguration $migConf, Database\DatabaseManager $dbal) {
        $config = new MigrationConfig($migConf->toArray());
        $migrator = new Migrator($config, $dbal, new FileRepository($config));
        // Init migration table
        $migrator->configure();
        return $migrator;
    }
];
