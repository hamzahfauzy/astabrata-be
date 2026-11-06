<?php

namespace Libraries\Logging;

use PDO;

class DatabaseLogDriver implements LogDriverInterface
{
    protected PDO $connection;
    protected string $table;

    public function __construct(PDO $connection, array $config)
    {
        $this->connection = $connection;
        $this->table = $config['table'] ?? 'request_logs';

        // Validasi nama tabel
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->table)) {
            throw new \InvalidArgumentException(
                'Invalid log table name'
            );
        }
    }

    public function write(array $data): bool
    {
        $sql = "INSERT INTO {$this->table}
                (
                    request_id,
                    log_type,
                    method,
                    url,
                    status_code,
                    payload,
                    ip_address,
                    duration,
                    created_at
                )
                VALUES
                (
                    :request_id,
                    :log_type,
                    :method,
                    :url,
                    :status_code,
                    :payload,
                    :ip_address,
                    :duration,
                    CURRENT_TIMESTAMP
                )";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            ':request_id' => $data['request_id'],
            ':log_type' => $data['log_type'],
            ':method' => $data['method'] ?? null,
            ':url' => $data['url'] ?? null,
            ':status_code' => $data['status_code'] ?? null,
            ':payload' => json_encode(
                $data['payload'] ?? null,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_PARTIAL_OUTPUT_ON_ERROR
            ),
            ':ip_address' => $data['ip_address'] ?? null,
            ':duration' => $data['duration'] ?? null,
        ]);
    }
}