<?php

namespace Libraries;

class JsonData
{
    function __construct(public string $message, public $data, public int $status = 200)
    {
    }

    function send()
    {
        http_response_code($this->status);
        header('Content-type: application/json');
        echo json_encode([
            'message' => $this->message,
            'data' => $this->data,
        ]);
    }
}