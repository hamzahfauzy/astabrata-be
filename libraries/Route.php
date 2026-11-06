<?php

namespace Libraries;

use Closure;

class Route
{
    /**
     * Semua route yang terdaftar.
     */
    protected static array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    protected static array $runtimeRoute = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    /**
     * Prefix group.
     */
    protected static string $prefix = '';

    public static function get(string $uri, ...$handlers): void
    {
        self::add('GET', $uri, $handlers);
    }

    public static function post(string $uri, ...$handlers): void
    {
        self::add('POST', $uri, $handlers);
    }

    public static function put(string $uri, ...$handlers): void
    {
        self::add('PUT', $uri, $handlers);
    }

    public static function patch(string $uri, ...$handlers): void
    {
        self::add('PATCH', $uri, $handlers);
    }

    public static function delete(string $uri, ...$handlers): void
    {
        self::add('DELETE', $uri, $handlers);
    }

    protected static function add(
        string $method,
        string $uri,
        array $handlers
    ): void {

        $uri = '/' . trim(self::$prefix . '/' . trim($uri, '/'), '/');
        $method = strtoupper($method);

        self::$routes[$method][] = [
            'method' => strtoupper($method),
            'uri'    => $uri,
            'compiled'  => self::compileRoute($uri),
            'handlers'  => $handlers,
        ];
    }

    public static function group(string $prefix, callable $callback): void
    {
        $oldPrefix = self::$prefix;
        self::$prefix .= '/' . trim($prefix, '/');
        $callback();
        self::$prefix = $oldPrefix;
    }

    public static function crud(string $uri, $config, $permissionPrefix = '*', ...$handlers)
    {
        $config = self::parseConfig($config);

        $defaultActions = [
            'index' => [Crud::class, 'index'],
            'store' => [Crud::class, 'store'],
            'update' => [Crud::class, 'update'],
            'destroy' => [Crud::class, 'destroy'],
            'show' => [Crud::class, 'show'],
        ];

        $defaultData = [
            'table' => '',
            'searchable'  => [],
            'filterable'  => [],
            'sortable'  => [],
            'list'  => '*',
            'view'  => '*'
        ];

        $otherData = array_merge($defaultData, $config['data']);
        $actions = array_merge($defaultActions, $config['actions'] ?? []);

        $permissions = [
            'index' => $permissionPrefix == '*' ? $permissionPrefix : $permissionPrefix.'index',
            'store' => $permissionPrefix == '*' ? $permissionPrefix : $permissionPrefix.'store',
            'update' => $permissionPrefix == '*' ? $permissionPrefix : $permissionPrefix.'update',
            'destroy' => $permissionPrefix == '*' ? $permissionPrefix : $permissionPrefix.'destroy',
            'show' => $permissionPrefix == '*' ? $permissionPrefix : $permissionPrefix.'show',
        ];

        self::add('GET', $uri, [...$handlers, permissionMiddleware($permissions['index']), crudConfig($otherData), $actions['index']]);
        self::add('POST', $uri, [...$handlers, permissionMiddleware($permissions['store']), crudConfig($otherData), $actions['store']]);
        self::add('GET', $uri . '/{id}', [...$handlers, permissionMiddleware($permissions['show']), crudConfig($otherData), $actions['show']]);
        self::add('PUT', $uri . '/{id}', [...$handlers, permissionMiddleware($permissions['update']), crudConfig($otherData), $actions['update']]);
        self::add('DELETE', $uri . '/{id}', [...$handlers, permissionMiddleware($permissions['destroy']), crudConfig($otherData), $actions['destroy']]);
    }

    protected static function parseConfig($config)
    {
        if (is_string($config) && file_exists($config .'.php')) {
            return require $config . '.php';
        }

        if (is_callable($config)) {
            return call_user_func($config);
        }

        if ($config instanceof Closure) {
            return $config();
        }

        return null;
    }

    public static function importCache($routes)
    {
        self::$routes = $routes;
    }

    public static function exportCache(): array
    {
        $cache = [];

        foreach (self::$routes as $method => $routeGroups) {
            foreach ($routeGroups as $route) {

                $handlers = $route['handlers'] ?? [];

                // Periksa apakah semua handler bisa di-cache
                $cacheable = true;

                foreach ($handlers as $handler) {

                    if ($handler instanceof \Closure) {
                        $cacheable = false;
                        break;
                    }

                }

                // Lewati route yang menggunakan Closure
                if (!$cacheable) {
                    self::$runtimeRoute[] = $route;
                    continue;
                }

                if(!isset($cache[$method]))
                {
                    $cache[$method] = [];
                }

                $cache[$method][] = $route;
            }

        }
        return $cache;
    }

    public static function all(): array
    {
        return self::$routes;
    }

    protected static function compileRoute($pattern)
    {
        preg_match_all('/\{(\w+)\}/', $pattern, $names);

        $regex = preg_replace(
            '/\{\w+\}/',
            '([^/]+)',
            $pattern
        );

        return [
            'regex' => '#^' . $regex . '$#',
            'params' => $names[1]
        ];
    }
}