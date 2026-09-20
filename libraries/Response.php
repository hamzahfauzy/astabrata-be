<?php

namespace Libraries;

class Response
{
    public static function send(mixed $data): void
    {
        if ($data === null) {
            http_response_code(204);
            return;
        }

        if (is_array($data)) {
            header('Content-Type: application/json');
            echo json_encode($data);
            return;
        }

        if (is_string($data)) {
            echo $data;
            return;
        }

        if (is_object($data)) {
            header('Content-Type: application/json');
            echo json_encode($data);
            return;
        }

        echo (string)$data;
    }

    public static function json(string $message, $data, $status = 200)
    {
        return new JsonData($message, $data, $status);
    }
}