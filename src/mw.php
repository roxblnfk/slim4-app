<?php

use App\Http\Middleware\HttpErrorMiddleware;
use Slim\App as Application;
use Psr\Container\ContainerInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;

return function (Application $app, ContainerInterface $container) : void {
    # Routes
    $app->add(RoutingMiddleware::class);

    # Custom HTTP Error Handler
    $app->add(HttpErrorMiddleware::class);

    # Slim Error Handler
    $errorMiddleware = new ErrorMiddleware($app->getCallableResolver(), $app->getResponseFactory(), true, true, true);
    // $errorMiddleware->setErrorHandler(\Slim\Exception\HttpNotFoundException::class, \App\Http\Handler\ErrorHandler::class);
    $app->add($errorMiddleware);

};
