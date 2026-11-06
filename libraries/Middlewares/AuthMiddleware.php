<?php

namespace Libraries\Middlewares;

use Libraries\Exceptions\UnauthorizedException;
use Libraries\Request;

class AuthMiddleware
{
    public static function handle(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            throw new UnauthorizedException([
                'message' => 'Unauthorized'
            ]);
        }

        $request->setUser($user);
    }
}