<?php

return [
    // file atau database
    'driver' => env('LOG_DRIVER', 'file'),

    // Konfigurasi file
    'file' => [
        'path' => 'logs',
        'filename' => 'request.log',
        'permission' => 0755,
    ],

    // Konfigurasi database
    'database' => [
        'table' => 'request_logs',
    ],

    // Data sensitif yang harus disamarkan
    'sensitive' => [
        'password',
        // 'token',
        // 'access_token',
        // 'refresh_token',
        // 'authorization',
        // 'secret',
        // 'api_key',
    ],

    // Aktifkan atau nonaktifkan logging
    'enabled' => env('LOG_STATUS', false),
];