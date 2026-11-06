<?php

namespace Libraries\Logging;

class FileLogDriver implements LogDriverInterface
{
    protected string $path;
    protected int $permission;

    public function __construct(array $config)
    {
        $this->path = rtrim($config['path'], '/');
        $this->permission = $config['permission'] ?? 0755;
    }

    public function write(array $data): bool
    {
        if (!is_dir($this->path)) {
            if (!mkdir($this->path, $this->permission, true)
                && !is_dir($this->path)) {
                throw new \RuntimeException(
                    'Unable to create log directory'
                );
            }
        }

        $filename = date('Y-m-d') . '.log';

        $file = $this->path . '/' . $filename;

        $content = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PARTIAL_OUTPUT_ON_ERROR
        );

        if ($content === false) {
            throw new \RuntimeException(
                'Unable to encode log data'
            );
        }

        return file_put_contents(
            $file,
            $content . PHP_EOL,
            FILE_APPEND | LOCK_EX
        ) !== false;
    }
}
