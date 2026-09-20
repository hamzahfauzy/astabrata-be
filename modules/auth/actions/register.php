<?php

use Libraries\Response;
use Libraries\Database as DB;

$payload = request()->except(['password_confirmation']);
$payload['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);

if(!isset($payload['username']) && isset($payload['email']))
{
    $payload['username'] = $payload['email'];
}

DB::table('users')->insert($payload);

return Response::json(
    __('Register success.'),
    []
);