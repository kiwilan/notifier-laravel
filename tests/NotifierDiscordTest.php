<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\LaravelNotifier\Facades\Notifier;

beforeEach(function () {
    Config::set('notifier.discord.webhook', dotenv()['NOTIFIER_DISCORD_WEBHOOK']);
    Config::set('notifier.discord.username', dotenv()['NOTIFIER_DISCORD_USERNAME']);
    Config::set('notifier.discord.avatar_url', dotenv()['NOTIFIER_DISCORD_AVATAR_URL']);
});

it('can use', function () {
    $notifier = Notifier::discord()
        ->message('Hello, Discord!')
        ->user('Notifier', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::discord()
        ->message([
            'Hello, Discord!',
            'This is a message.',
        ])
        ->user('Notifier', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::discord()
        ->rich([
            'Rich',
            'simple',
            'for',
            'Discord',
        ])
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::discord()
        ->rich('Rich advanced')
        ->title('Notifier')
        ->user('Notifier', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->url('https://ewilan-riviere.com')
        ->author('Author', 'https://ewilan-riviere.com', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->color('#3498db')
        ->timestamp(now())
        ->fields([
            ['name' => 'Field 1', 'value' => 'Value 1'],
            ['name' => 'Field 2', 'value' => 'Value 2'],
        ], inline: true)
        ->thumbnail('https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->image('https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->footer('Footer', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $toArray = $notifier->toArray();
    $toArray['embeds'][0]['timestamp'] = null;
    expect($toArray)->toBe([
        'username' => 'Notifier',
        'avatar_url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
        'embeds' => [
            [
                'author' => [
                    'name' => 'Author',
                    'url' => 'https://ewilan-riviere.com',
                    'icon_url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                ],
                'title' => 'Notifier',
                'url' => 'https://ewilan-riviere.com',
                'type' => 'rich',
                'description' => 'Rich advanced',
                'fields' => [
                    [
                        'name' => 'Field 1',
                        'value' => 'Value 1',
                        'inline' => true,
                    ],
                    [
                        'name' => 'Field 2',
                        'value' => 'Value 2',
                        'inline' => true,
                    ],
                ],
                'color' => 3447003,
                'thumbnail' => [
                    'url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                ],
                'image' => [
                    'url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                ],
                'footer' => [
                    'text' => 'Footer',
                    'icon_url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                ],
                'timestamp' => null,
            ],
        ],
    ]);
});

it('can use command', function () {
    $success = Artisan::call('notifier', [
        'message' => 'Hello, Discord with webhook!',
        '--type' => 'discord',
        '--webhook' => config('notifier.discord.webhook'),
    ]);
    expect($success)->toBe(0);

    $success = Artisan::call('notifier', [
        'message' => 'Hello, Discord with config!',
        '--type' => 'discord',
    ]);
    expect($success)->toBe(0);
});
