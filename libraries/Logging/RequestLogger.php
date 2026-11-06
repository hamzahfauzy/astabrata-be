<?php

namespace Libraries\Logging;

class RequestLogger
{
    protected Logger $logger;

    protected string $requestId;

    protected float $startTime;

    protected array $request;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;

        $this->requestId = bin2hex(random_bytes(16));

        $this->startTime = microtime(true);

        $this->request = $this->captureRequest();

        $this->writeRequestLog();
    }

    protected function captureRequest(): array
    {
        $payload = $_POST;

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $body = file_get_contents('php://input');

            $decoded = json_decode($body, true);

            $payload = is_array($decoded)
                ? $decoded
                : ['raw' => $body];
        }

        return [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'url' => $_SERVER['REQUEST_URI'] ?? null,
            'headers' => function_exists('getallheaders')
                ? getallheaders()
                : [],
            'payload' => $payload,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];
    }

    protected function writeRequestLog(): void
    {
        $user = request()->user() ?? [];
        $this->logger->log([
            'datetime' => date('Y-m-d H:i:s'),
            'request_id' => $this->requestId,
            'log_type' => 'request',
            'method' => $this->request['method'],
            'url' => $this->request['url'],
            'payload' => $this->logger->sanitize([
                'user' => $user,
                'headers' => $this->request['headers'],
                'body' => $this->request['payload'],
            ]),
            'ip_address' => $this->request['ip_address'],
        ]);
    }

    public function logResponse(
        int $statusCode,
        mixed $response
    ): void {
        $duration = round(
            (microtime(true) - $this->startTime) * 1000,
            2
        );

        $this->logger->log([
            'request_id' => $this->requestId,
            'log_type' => 'response',
            'method' => $this->request['method'],
            'url' => $this->request['url'],
            'status_code' => $statusCode,
            'payload' => $this->logger->sanitize([
                'body' => $response,
            ]),
            'ip_address' => $this->request['ip_address'],
            'duration' => $duration,
        ]);
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }
}