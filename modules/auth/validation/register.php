<?php

return [
    'name' => [
        'required',
        'min:3'
    ],
    'email' => [
        'required',
        'email',
        'unique:users,email'
    ],
    'password' => [
        'required',
        'min:8',
        'confirmed'
    ]
];