<?php

namespace Libraries;

use Closure;
use Libraries\Exceptions\NotFoundException;
use Libraries\Exceptions\UnauthorizedException;
use Libraries\Exceptions\ValidationException;
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
        require 'config/routes.php';

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

        return;

    }

    protected function match(string $pattern, string $uri): array|false
    {
        preg_match_all('/\{(\w+)\}/', $pattern, $names);

        $regex = preg_replace(
            '/\{\w+\}/',
            '([^/]+)',
            $pattern
        );

        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return false;
        }

        array_shift($matches);

        return array_combine(
            $names[1],
            $matches
        );
    }

    public function dispatch()
    {
        $request = request();

        foreach ($this->routes as $route) {

            if(isset($_POST['_method']))
            {
                $request->setMethod($_POST['_method']);
            }

            if ($route['method'] !== $request->method()) {
                continue;
            }

            $params = $this->match(
                $route['uri'],
                $request->path()
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
                $middleware($request);
            }

            return $this->runAction($action, $request);
        }

        throw new NotFoundException(['message' => __('Not Found')]);
        
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