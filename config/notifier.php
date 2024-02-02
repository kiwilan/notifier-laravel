<?php

return [
    'discord' => [
        'webhook' => env('NOTIFIER_DISCORD_WEBHOOK', null),
        'username' => env('NOTIFIER_DISCORD_USERNAME', null),
        'avatar_url' => env('NOTIFIER_DISCORD_AVATAR_URL', null),
    ],

    'slack' => [
        'webhook' => env('NOTIFIER_SLACK_WEBHOOK', null),
    ],

    'mail' => [
        'mailer' => env('NOTIFIER_MAIL_MAILER', 'smtp'),
        'host' => env('NOTIFIER_MAIL_HOST', 'mailpit'),
        'port' => env('NOTIFIER_MAIL_PORT', 1025),
        'username' => env('NOTIFIER_MAIL_USERNAME', null),
        'password' => env('NOTIFIER_MAIL_PASSWORD', null),
        'encryption' => env('NOTIFIER_MAIL_ENCRYPTION', 'tls'),
        'from_address' => env('NOTIFIER_MAIL_FROM_ADDRESS', null),
        'from_name' => env('NOTIFIER_MAIL_FROM_NAME', null),
        'to_address' => env('NOTIFIER_MAIL_TO_ADDRESS', null),
        'to_name' => env('NOTIFIER_MAIL_TO_NAME', null),
    ],
];
