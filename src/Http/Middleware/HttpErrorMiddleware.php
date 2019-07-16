<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Helper\Render\RendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class HttpErrorMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;
    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param RendererInterface        $renderer
     */
    public function __construct(ResponseFactoryInterface $responseFactory, RendererInterface $renderer)
    {
        $this->responseFactory = $responseFactory;
        $this->renderer = $renderer;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->handleException($request, $e);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable              $exception
     * @return ResponseInterface
     * @throws Throwable
     */
    public function handleException(ServerRequestInterface $request, Throwable $exception): ResponseInterface
    {
        if (!$exception instanceof HttpException) {
            throw $exception;
        }
        /** @var HttpException $exception */
        $this->renderer->layout('error', [
            'error' => $exception,
            'title' => 'Error page',
        ]);
        $response = $this->responseFactory->createResponse(200);
        $response->getBody()->write($this->renderer->render(null));

        return $response;
    }
}
