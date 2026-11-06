<?php

namespace Libraries\Logging;

class Logger
{
    protected LogDriverInterface $driver;

    protected array $config;

    public function __construct(array $config, $connection = null)
    {
        $this->config = $config;

        if (!$config['enabled']) {
            return;
        }

        $this->driver = match ($config['driver']) {
            'file' => new FileLogDriver($config['file']),

            'database' => new DatabaseLogDriver(
                $connection,
                $config['database']
            ),

            default => throw new \InvalidArgumentException(
                'Unsupported log driver'
            ),
        };
    }

    public function log(array $data): void
    {
        if (!$this->config['enabled']) {
            return;
        }

        try {
            $this->driver->write($data);
        } catch (\Throwable $e) {
            // Logging tidak boleh menggagalkan request utama.
            error_log(
                '[Logger] ' . $e->getMessage()
            );
        }
    }

    public function sanitize(array $data): array
    {
        $sensitive = $this->config['sensitive'] ?? [];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $data[$key] = '********';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            }
        }

        return $data;
    }
}