<?php
namespace App\Console\Command;

use App\Config\MigrationConfiguration;
use Cycle\Annotated;
use Cycle\Migrations\GenerateMigrations;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Database;
use Spiral\Migrations;
use Spiral\Migrations\Migrator;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class MigrateGenerateCommand extends Command
{
    protected static $defaultName = 'migrate:generate';

    /** @var Database\DatabaseManager $dbal */
    protected $dbal;
    /** @var MigrationConfiguration */
    private $config;
    /**
     * @var Migrator
     */
    private $migrator;
    /**
     * MigrateGenerateCommand constructor.
     * @param Migrator                 $migrator
     * @param Database\DatabaseManager $dbal
     * @param MigrationConfiguration   $conf
     */
    public function __construct(Migrator $migrator, Database\DatabaseManager $dbal, MigrationConfiguration $conf)
    {
        $this->migrator = $migrator;
        $this->dbal = $dbal;
        $this->config = $conf;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Migration generator',
            '===================',
            '',
        ]);
        $finder = (new Finder())->files()->in($this->config->entities);
        $classLocator = new ClassLocator($finder);
        $schema = (new Compiler())->compile(new Registry($this->dbal), [
            new Annotated\Embeddings($classLocator),                  # register embeddable entities
            new Annotated\Entities($classLocator),                    # register annotated entities
            new ResetTables(),                                        # re-declared table schemas (remove columns)
            new GenerateRelations(),                                  # generate entity relations
            new ValidateEntities(),                                   # make sure all entity schemas are correct
            new RenderTables(),                                       # declare table schemas
            new RenderRelations(),                                    # declare relation keys and indexes
            new GenerateMigrations($this->migrator->getRepository()), # generate migrations
            new GenerateTypecast(),                                   # typecast non string columns
        ]);
    }

}
