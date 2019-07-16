<?php

use App\Config\ApplicationListConfiguration;
use App\Config\MigrationConfiguration;
use App\Http\Controller\IndexController;
use App\Http\Controller\PictureController;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;
use Symfony\Component\Config\Definition\Processor;

$srcDir = __DIR__;
chdir(dirname($srcDir));
require_once 'vendor/autoload.php';

$builder = new ContainerBuilder();

$appsConf = new ApplicationListConfiguration(new Processor(), 'config/application.yaml');
$appConf = $appsConf->determineApplication();

$builder->addDefinitions($appConf->getContainerDefinitions($srcDir . '/Container'));

$container = $builder->build();

AppFactory::setContainer($container);
AppFactory::setResponseFactory($container->get(ResponseFactoryInterface::class));
AppFactory::setCallableResolver($container->get(CallableResolverInterface::class));
AppFactory::setRouteCollector($container->get(RouteCollectorInterface::class));
AppFactory::setRouteResolver($container->get(RouteResolverInterface::class));
$app = AppFactory::create();

// d($container->get('database')->getTables());
/** @var Cycle\ORM\ORM $orm */
// $orm = $container->get('orm');
// d($orm->getSchema());
// d($classLocator->getClasses());

(require "$srcDir/mw.php")($app, $container);

// $app->any('/picture[/{action}]', PictureController::class);
$app->any('/[{page}]', IndexController::class);

$app->run();
