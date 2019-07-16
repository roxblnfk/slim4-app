<?php

use App\Helper\Render;
use App\Helper\Render\RendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\CallableResolver;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteParser;
use Slim\Routing\RouteResolver;

return [
    # slim application:
    ResponseFactoryInterface::class    => DI\create(ResponseFactory::class),
    CallableResolverInterface::class   => function ($c) { return new CallableResolver($c); },
    RouteCollectorInterface::class     => function ($c) {
        return new RouteCollector(
            $c->get(ResponseFactoryInterface::class),
            $c->get(CallableResolverInterface::class),
            $c,
            $c->get(InvocationStrategyInterface::class),
            null,
            $cacheFile = null
        );
    },
    RouteResolverInterface::class      => DI\autowire(RouteResolver::class),
    # Routing strategy
    InvocationStrategyInterface::class => DI\create(RequestResponseArgs::class),
    # http
    StreamFactoryInterface::class      => DI\create(StreamFactory::class),

    # set the pages renderer: Render\PhugRenderer | Render\PlatesRenderer | Render\TwigRenderer
    RendererInterface::class           => DI\get(Render\PhugRenderer::class),
];
