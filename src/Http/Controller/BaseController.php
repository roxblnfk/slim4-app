<?php
namespace App\Http\Controller;

use App\Helper\Render\RendererInterface;
use League\Plates\Engine;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Slim\Exception\HttpException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class BaseController
{
    /** @var Response */
    protected $response;
    /** @var mixed[] */
    protected $ajax = ['response' => true];
    /** @var string[] */
    protected $messages = [];
    /** @var Container */
    protected $container;
    /** @var StreamFactory */
    protected $streamFactory;
    /** @var RendererInterface */
    protected $renderer;
    /** @var string */
    protected $pageLayout = 'default';
    protected $pageLayoutData = [];
    // /** @var string */

    /**
     * baseController constructor.
     * @param Container $container
     * @param mixed[]   $options
     */
    public function __construct(Container $container, $options = [])
    {
        $this->container = $container;
        $this->streamFactory = $container->get(StreamFactory::class);
        $this->renderer = $this->renderer ?? $container->get(RendererInterface::class);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws HttpException
     * @throws Throwable
     */
    public function __invoke(Request $request, Response $response, $args = []): Response
    {
        try {
            return $this->wrapResponse($this->handleRequest($request, $response, $args));
        } catch (Throwable $e) {
            return $this->handleError($e);
        }
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws HttpException
     * @throws Throwable
     */
    protected function handleRequest(Request $request, Response $response, $args = [])
    {
        $this->response = $response;
        $method = $request->getMethod();
        # find action
        $page = $args['page'] ?? 'index';
        $action = $args['action'] ?? $_POST['action'] ?? $_GET['action'] ?? null;
        if ($action OR $method === 'POST') {
            $action = $action ?? $page;
            $method = 'action' . str_replace(' ', '', ucwords($action));
            if (method_exists($this, $method)) {
                return method_exists($this->container, 'call')
                    ? $this->container->call([$this, $method], ['request' => $request, 'response' => $response, 'args' => $args])
                    : $this->$method($request, $args);
            } else {
                throw new HttpMethodNotAllowedException($request);
            }
        } elseif ($method === 'GET') {
            $method = 'page' . str_replace(' ', '', ucwords($page));
            if (method_exists($this, $method)) {
                return method_exists($this->container, 'call')
                    ? $this->container->call([$this, $method], ['request' => $request, 'response' => $response, 'args' => $args])
                    : $this->$method($request, $args);
            } else {
                throw new HttpNotFoundException($request);
            }
        }
        return $this->response;
    }

    /**
     * Redeclare this function if u want to wrap the content
     * @param Response|string $response
     * @return Response
     */
    protected function wrapResponse($response): Response
    {
        if ($response instanceof Response) {
            $this->response = $response;
            /*  String content meta example
                'wrapper_type' => string(3) "PHP"
                'stream_type' => string(4) "TEMP"
                'mode' => string(3) "w+b"
                'unread_bytes' => integer0
                'seekable' => boolTRUE
                'uri' => string(10) "php://temp"
            */
            /*  FILE meta example
                'timed_out' => boolFALSE
                'blocked' => boolTRUE
                'eof' => boolFALSE
                'wrapper_type' => string(9) "plainfile"
                'stream_type' => string(5) "STDIO"
                'mode' => string(1) "r"
                'unread_bytes' => integer0
                'seekable' => boolTRUE
                'uri' => string(64) "../file/name.zip"
            */
            $body = $response->getBody();
            $meta = $body->getMetadata();
            if ($meta['blocked'] ?? false OR !$body->isSeekable()) {
                return $response;
            }
            $body->rewind();
        }
        # get content
        // $content = $response instanceof Response
        //     ? $response->getBody()->getContents()
        //     : (string)$response;
        // if (!strlen($content)) ...
        return $this->response;
    }

    /**
     * @return bool|Response
     */
    public function checkFormToken() {
        return true;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        static $result = null;
        if (is_bool($result))
            return $result;
        if (function_exists('getallheaders')) {
            $allHeaders = getallheaders();
            $var_key = 'x-requested-with';
            $var_value = 'XMLHttpRequest';
        } else {
            $allHeaders = $_SERVER;
            $var_key = 'http_x_requested_with';
            $var_value = 'XMLHttpRequest';
        }
        foreach ($allHeaders as $name => $value) {
            if (strtolower($name) === $var_key && $value === $var_value) {
                return $result = true;
            }
        }
        return $result = false;
    }

    /**
     * @param string|string[] $message
     * @param null            $forceAjax
     * @return Response
     */
    protected function prepareResponse($message = '', $forceAjax = null): Response {
        if ($message) {
            $message = (array)$message;
            foreach($message as $m) {
                $this->messages[] = is_scalar($m) ? $m : print_r($m, true);
            }
        }
        if (is_bool($forceAjax) ? $forceAjax : $this->isAjax()) {
            $this->response = $this->response->withHeader('Content-Type', 'application/json;charset=utf-8');
            $this->response->getBody()
                           ->write(json_encode(
                               array_merge($this->ajax, array('message' => implode("<br>\r\n", $this->messages))),
                               JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR
                           ));
            return $this->response;
        }
        if (isset($this->ajax['url'])) {
            return $this->response->withStatus(302)->withHeader('Location', $this->ajax['url']);
        }
        $this->response->getBody()
                       ->write(implode("\r\n", $this->messages));
        return $this->wrapResponse($this->response);
    }

    /**
     * @param Throwable $error
     * @return Response
     * @throws Throwable
     */
    protected function handleError(Throwable $error): Response
    {
        if ($error instanceof ControllerException) {
            return $this->prepareResponse($error->getMessage());
        }
        if ($error instanceof Throwable) {
            throw $error;
        }
        return $this->wrapResponse($this->response);
    }
    /**
     * @param string  $template
     * @param mixed[] $data
     * @return Response
     * @throws Throwable
     */
    protected function render(string $template, array $data = []): Response
    {
        $this->renderer->layout($this->pageLayout, $this->pageLayoutData);
        $this->response->getBody()->write($this->renderer->render($template, $data));
        return $this->response;
    }
}
