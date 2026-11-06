<?php

namespace Libraries\Middlewares;

use Libraries\Exceptions\UnauthorizedException;
use Libraries\Request;

class PermissionMiddleware
{
    public static function handle(Request $request, $permission): bool
    {
        if (!auth()->can($permission)) {
            throw new UnauthorizedException([
                'message' => 'Unauthorized'
            ]);
        }

        return true;
    }
}