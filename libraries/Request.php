<?php

namespace Libraries;

class Request
{
    protected string $method;
    protected string $path;

    protected array $query;
    protected array $body;
    protected array $files;
    protected array $cookies;
    protected array $server;

    protected array $otherData;

    protected ?object $user = null;

    /**
     * Route parameters.
     */
    protected array $params = [];

    public function __construct()
    {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $this->path = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        $this->query   = $_GET;
        $this->body    = $this->parseBody();
        $this->files   = $_FILES;
        $this->cookies = $_COOKIE;
        $this->server  = $_SERVER;
    }

    protected function parseBody(): array
    {
        if (in_array($this->method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {

            if (!empty($_POST)) {
                return $_POST;
            }

            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (str_contains($contentType, 'application/json')) {

                $json = json_decode(
                    file_get_contents('php://input'),
                    true
                );

                return is_array($json) ? $json : [];
            }

            parse_str(
                file_get_contents('php://input'),
                $data
            );

            return $data;
        }

        return [];
    }

    public function setOtherData(string $key, mixed $data): void
    {
        $this->otherData[$key] = $data;
    }

    public function otherData(?string $key = null, mixed $default = null): mixed
    {
        $data = $this->otherData;

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        $data = array_merge($this->query, $this->body);

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function body(?string $key = null, mixed $default = null): mixed
    {
        unset($this->body['_method']);
        
        if ($key === null) {
            return $this->body;
        }

        return $this->body[$key] ?? $default;
    }

    public function file(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->files;
        }

        return $this->files[$key] ?? null;
    }

    public function cookie(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->cookies;
        }

        return $this->cookies[$key] ?? null;
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        return $this->server[$key] ?? null;
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function isAjax(): bool
    {
        return strtolower(
            $this->header('X-Requested-With') ?? ''
        ) === 'xmlhttprequest';
    }

    public function isJson(): bool
    {
        return str_contains(
            $this->server['CONTENT_TYPE'] ?? '',
            'application/json'
        );
    }

    public function all(): array
    {
        return $this->input();
    }

    public function except($fields)
    {
        $input = $this->all();
        foreach($fields as $field)
        {
            unset($input[$field]);
        }

        return $input;
    }

    /**
     * Set route parameters.
     */
    public function setRouteParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get route parameter.
     */
    public function params(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->params;
        }

        $param = $this->params[$key] ?? $default;

        return urldecode($param);
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization');

        if (!$header) {
            return null;
        }

        if (preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function user()
    {
        return $this->user;
    }

    public function validate(array $rules): void
    {
        Validator::make(
            $this->all(),
            $rules
        )->validate();
    }
}