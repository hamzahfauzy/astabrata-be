<?php

return [
    'email' => [
        'required',
        'exists:users,email'
    ],
    'password' => [
        'required'
    ]
];