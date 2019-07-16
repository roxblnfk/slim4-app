<?php
/**
 * © roxblnfk 2019
 */
namespace App\Http\Handler;

use APP;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{

    private $responseFactory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->responseFactory = $factory;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200);
        $response->getBody()->write(\Kint::dump($exception));

        return $response;
    }
}
