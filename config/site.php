<?php

return [
    'front_website_url' => env('FRONT_WEBSITE_URL', 'http://127.0.0.1:8000'),
    'pagination' => [
        'limit' => 10,
    ],
    'user_status' => [
        'active' => 'active',
        'inactive' => 'inactive',
    ],
    'roles' => [
        'admin' => 'admin',
        'user' => 'user',
    ],
    'roleIds' => [
        'admin' => 1,
        'user' => 2,
    ],
    'otp' => [
        'expiration_time_in_minutes' => 10,
        'length' => 6,
        'types' => [
            'login' => 'login',
            'register' => 'register',
            'forgot_password' => 'forgot_password',
            'verification' => 'verification',
        ],
    ],
];
