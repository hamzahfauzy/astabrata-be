<?php

use Libraries\Response;
use Libraries\Database as DB;

$user = request()->user();
$user = DB::table('users')->where('id', $user->id)->first();
$payload = request()->except(['password_confirmation','_method']);
if(!empty($payload['password']))
{
    $payload['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);
}
else
{
    $payload['password'] = $user->password;
}

DB::table('users')->where('id',$user->id)->update($payload);

return Response::json(
    __('Update success.'),
    auth()->user()
);