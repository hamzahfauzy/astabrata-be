<?php

namespace Libraries\Middlewares;

use Libraries\Request;

class ValidateMiddleware
{
    public static function handle(Request $request, string $file): void
    {
        $path = $file . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException(
                "Validation file '{$path}' tidak ditemukan."
            );
        }

        $rules = require $path;

        $request->validate($rules);
    }
}