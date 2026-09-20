<?php

$email = request()->input('email');
$password = request()->input('password');


if (!auth()->attempt($email, $password)) {

    return \Libraries\Response::json(
        __('Wrong email or password.'),
        [],
        401
    );

}

$token = auth()->login();

return \Libraries\Response::json(
    __('Login success.'),
    [
        'token' => $token,
    ]
);