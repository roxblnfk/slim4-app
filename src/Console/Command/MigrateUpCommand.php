<?php
namespace App\Console\Command;

use App\Config\MigrationConfiguration;
use Spiral\Database;
use Spiral\Migrations;
use Spiral\Migrations\MigrationInterface;
use Spiral\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateUpCommand extends Command
{
    protected static $defaultName = 'migrate:up';

    /** @var Database\DatabaseManager $dbal */
    protected $dbal;
    /** @var MigrationConfiguration */
    private $config;
    /** @var Migrator */
    private $migrator;
    /**
     * MigrateGenerateCommand constructor.
     * @param Migrator                 $migrator
     * @param Database\DatabaseManager $dbal
     * @param MigrationConfiguration   $conf
     */
    public function __construct(Migrator $migrator, Database\DatabaseManager $dbal, MigrationConfiguration $conf)
    {
        $this->dbal = $dbal;
        $this->config = $conf;
        $this->migrator = $migrator;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Migration runner',
            '================',
            '',
        ]);
        $list = $this->migrator->getMigrations();
        $output->writeln(count($list) . ' migrations found in ' . $this->config->directory);

        $limit = PHP_INT_MAX;
        $statuses = [-1 => 'undefined', 0 => 'pending', 1 => 'executed'];
        try {
            do {
                $migration = $this->migrator->run();
                if (!$migration instanceof MigrationInterface) break;

                $state = $migration->getState();
                $status = $state->getStatus();
                $output->writeln($state->getName() . ': ' . ($statuses[$status] ?? $status));
            } while (--$limit > 0);
        } catch (\Throwable $e) {
            $output->writeln([
                '===================',
                'Error!',
                $e->getMessage(),
            ]);
            return;
        }
    }
}
