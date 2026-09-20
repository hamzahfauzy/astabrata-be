<?php

$user = request()->user();

return [
    'name' => [
        'required',
        'min:3'
    ],
    'email' => [
        'required',
        'email',
        'unique:users,email,'.$user->id
    ],
    'username' => [
        'required',
        'email',
        'unique:users,username,'.$user->id
    ],
    'password' => [
        'nullable',
        'min:8'
    ]
];