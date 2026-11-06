<?php

namespace Libraries;

use Closure;
use Libraries\Exceptions\NotFoundException;
use Libraries\Exceptions\UnauthorizedException;
use Libraries\Exceptions\ValidationException;
use Libraries\Logging\Logger;
use Libraries\Logging\RequestLogger;
use RuntimeException;

class Application 
{

    public $routes = [];

    public function __construct()
    {
        
    }

    public function run()
    {
        require 'libraries/Functions.php';

        $config = require 'config/logger.php';

        // Inisialisasi driver
        $connection = $config['driver'] === 'database' ? Database::getConnection() : null;

        $logger = new Logger($config, $connection);

        $requestLogger = new RequestLogger($logger);

        ob_start();

        try {
            
            $cache = new RouteCache('cache/routes.php');

            if ($cache->exists()) {

                $routes = $cache->load();

                if ($routes !== null) {
                    Route::importCache($routes);
                } else {
                    // Fallback jika cache rusak
                    require 'config/routes.php';
                }

            } else {
                require 'config/routes.php';
            }

            $this->routes = Route::all();
            try {

                $response = $this->dispatch();

                if($response instanceof JsonData)
                {
                    $response->send();
                }
                else
                {
                    Response::send($response);
                }

            } catch (ValidationException $e) {

                Response::json(
                    __('Validation Error'),
                    $e->errors(),
                    400
                )->send();

            } catch (UnauthorizedException $e) {

                Response::json(__('Unauthorized'), [], 401)->send();

            } catch (NotFoundException $e) {

                Response::json(__('Not Found'), [], 404)->send();

            } catch (\Throwable $e) {

                Response::json(
                    $e->getMessage(),
                    [],
                    500
                )->send();

            }

            $responseBody = ob_get_clean();

            $statusCode = http_response_code() ?: 200;

            // Konversi JSON response menjadi array jika memungkinkan
            $responsePayload = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $responsePayload = $responseBody;
            }

            // Catat response
            $requestLogger->logResponse(
                $statusCode,
                $responsePayload
            );

            echo $responseBody;

        } catch (\Throwable $th) {
            //throw $th;
            ob_end_clean();

            $requestLogger->logResponse(
                500,
                [
                    'error' => 'Internal Server Error',
                ]
            );

            throw $e;
        }

        return;

    }

    protected function match(array $compiled, string $uri): array|false
    {
        if (!preg_match($compiled['regex'], $uri, $matches)) {
            return false;
        }

        array_shift($matches);

        return $compiled['params']
            ? array_combine($compiled['params'], $matches)
            : [];
    }

    public function dispatch()
    {
        $request = request();

        if (isset($_POST['_method'])) {
            $request->setMethod(
                strtoupper($_POST['_method'])
            );
        }

        $method = $request->method();
        $path   = $request->path();
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {   

            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match(
                $route['compiled'],
                $path
            );

            if ($params === false) {
                continue;
            }

            $handlers = $route['handlers'];

            if (empty($handlers)) {
                throw new RuntimeException(
                    __("Route '{$route['uri']}' tidak memiliki action.")
                );
            }

            $action = array_pop($handlers);

            $request->setRouteParams($params);

            foreach ($handlers as $middleware) {
                $this->runMiddleware($middleware, $request);
            }

            return $this->runAction($action, $request);
        }

        throw new NotFoundException(['message' => __('Not Found')]);
        
    }

    protected function runMiddleware($middleware, Request $request): void
    {
        // Closure middleware
        if ($middleware instanceof Closure) {
            $middleware($request);
            return;
        }

        // Callable array middleware:
        // [Class, method, ...arguments]
        if (
            is_array($middleware) &&
            count($middleware) >= 2 &&
            is_string($middleware[0]) &&
            is_string($middleware[1])
        ) {
            [$class, $method] = array_slice($middleware, 0, 2);

            $arguments = array_slice($middleware, 2);

            if (!is_callable([$class, $method])) {
                throw new RuntimeException(
                    "Invalid middleware: {$class}::{$method}"
                );
            }

            $class::$method($request, ...$arguments);
            return;
        }

        throw new RuntimeException(__('Invalid middleware.'));
    }

    protected function runAction($action, Request $request)
    {
        if (is_string($action)) {
            return require $action . '.php';
        }

        if ($action instanceof Closure) {
            return $action($request);
        }

        if ($this->isControllerAction($action)) {
            [$class, $method] = $action;

            return (new $class())->$method($request);
        }

        throw new RuntimeException(__('Invalid route action.'));
    }

    protected function isControllerAction(mixed $action): bool
    {
        return is_array($action)
            && count($action) === 2
            && is_string($action[0])
            && is_string($action[1]);
    }

}