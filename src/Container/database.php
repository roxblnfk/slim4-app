<?php

use App\Config\DatabaseConfiguration;
use App\Config\MigrationConfiguration;
use Cycle\ORM;
use Spiral\Database;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\FileRepository;
use Spiral\Migrations\Migrator;

# DataBase
return [

    # config
    DatabaseConfiguration::class => DI\autowire(DatabaseConfiguration::class)
        ->constructorParameter('files', ['config/database.yaml']),

    # dbal
    Database\DatabaseManager::class      => function (DatabaseConfiguration $conf) {
        return new Database\DatabaseManager(new Database\Config\DatabaseConfig($conf->toArray()));
    },
    'database'                           => function (Database\DatabaseManager $dbal) {
        return $dbal->database('default');
    },
    'orm'                                => function (Database\DatabaseManager $dbal) {
        // $finder = (new \Symfony\Component\Finder\Finder())->files()->in([__DIR__ . "/Entity"]);
        // $cl = new \Spiral\Tokenizer\ClassLocator($finder);
        // $schema = (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
        //     new Annotated\Embeddings($cl),            // register embeddable entities
        //     new Annotated\Entities($cl),              // register annotated entities
        //     new Schema\Generator\ResetTables(),       // re-declared table schemas (remove columns)
        //     new Schema\Generator\GenerateRelations(), // generate entity relations
        //     new Schema\Generator\ValidateEntities(),  // make sure all entity schemas are correct
        //     new Schema\Generator\RenderTables(),      // declare table schemas
        //     new Schema\Generator\RenderRelations(),   // declare relation keys and indexes
        //     new Schema\Generator\SyncTables(),        // sync table changes to database
        //     new Schema\Generator\GenerateTypecast(),  // typecast non string columns
        // ]);

        $orm = new ORM\ORM(new ORM\Factory($dbal));
        // $orm = $orm->withSchema(new ORM\Schema($schema));

        return $orm;
    },
];
