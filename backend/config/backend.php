<?php

/**
 * The list of domains hosting your central app.
 *
 * Only relevant if you're using the domain or subdomain identification middleware.
 */
if (config('app.env') === 'production') {
    $domain = [
        'api.backend.test',
    ];
} else {
    $domain = [
        //        '127.0.0.1',
        //        'localhost:8000',
        //        'localhost',
        'backend.test',
    ];
}

return [

    'central_domains' => $domain,
    'application_ip' => env('APPLICATION_IP', '18.189.35.30'), //'18.189.35.38'
    'aws_region' => env('AWS_REGION', 'us-east-2'),

    'application_disk' => env('APPLICATION_DISK', \App\Enums\Disk::PUBLIC->value),

    'media' => [
        'profile' => [
            'path' => env('PROFILE_IMAGE', 'media/116c2708-ea98-483b-a9d0-6d85cdec4b2d-2023-08-14-11-41-55.jpeg'),
            'disk' => env('PLACEHOLDER_IMAGE_DISK', \App\Enums\Disk::PUBLIC->value),
        ],
        'image' => [
            'path' => env('PLACEHOLDER_IMAGE', 'media/9085cf26-4dac-463f-ae89-07543b1319b1-2023-08-14-11-45-14.jpeg'),
            'disk' => env('PLACEHOLDER_IMAGE_DISK', \App\Enums\Disk::PUBLIC->value),
        ],
        'video' => [
            'path' => env('PLACEHOLDER_VIDEO', 'media/9085cf26-4dac-463f-ae89-07543b1319b1-2023-08-22-11-45-34.mp4'),
            'disk' => env('PLACEHOLDER_VIDEO_DISK', \App\Enums\Disk::PUBLIC->value),
        ],

    ],
    'format' => [
        'format' => env('FORMAT_CURRENCY', false),

        'decimals' => 2,

        'decimal_point' => '.',

        'thousand_seperator' => ',',

    ],

    'tax' => env('TAX_RATE', 0),

    'binary' => [
        'node' => env('NODE_BINARY', '/usr/bin/node'),
        'npm' => env('NPM_BINARY', '/usr/bin/npm'),
        'chrome' => env('CHROME_BINARY', '/usr/bin/google-chrome'),
    ],
    'roles' => [
        'staff' => [
            'order' => 1,
            'description' => 'Staff user',
        ],
        'developer' => [
            'order' => 1,
            'description' => 'The Developer',
        ],
        'manager' => [
            'order' => 1,
            'description' => 'The manager',
        ],
        'designer' => [
            'order' => 1,
            'description' => 'The Designer',
        ],
        'scrum master' => [
            'order' => 1,
            'description' => 'The scrum master',
        ],
        'admin' => [
            'order' => 1,
            'description' => 'The admin',
        ],
    ],
];
