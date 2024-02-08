<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\Notifier\Facades\Notifier;
use Kiwilan\Notifier\Notifier as NotifierNotifier;

beforeEach(function () {
    Config::set('notifier.discord.webhook', dotenv()['NOTIFIER_DISCORD_WEBHOOK']);
    Config::set('notifier.discord.username', dotenv()['NOTIFIER_DISCORD_USERNAME']);
    Config::set('notifier.discord.avatar_url', dotenv()['NOTIFIER_DISCORD_AVATAR_URL']);
    Config::set('notifier.slack.webhook', dotenv()['NOTIFIER_SLACK_WEBHOOK']);
    Config::set('notifier.mail.mailer', dotenv()['NOTIFIER_MAIL_MAILER']);
    Config::set('notifier.mail.host', dotenv()['NOTIFIER_MAIL_HOST']);
    Config::set('notifier.mail.port', dotenv()['NOTIFIER_MAIL_PORT']);
    Config::set('notifier.mail.username', dotenv()['NOTIFIER_MAIL_USERNAME']);
    Config::set('notifier.mail.password', dotenv()['NOTIFIER_MAIL_PASSWORD']);
    Config::set('notifier.mail.encryption', dotenv()['NOTIFIER_MAIL_ENCRYPTION']);
    Config::set('notifier.mail.from.address', dotenv()['NOTIFIER_MAIL_FROM_ADDRESS']);
    Config::set('notifier.mail.from.name', dotenv()['NOTIFIER_MAIL_FROM_NAME']);
    Config::set('notifier.mail.to.address', dotenv()['NOTIFIER_MAIL_TO_ADDRESS']);
    Config::set('notifier.mail.to.name', dotenv()['NOTIFIER_MAIL_TO_NAME']);
});

it('can use instance', function () {
    $facade = Notifier::discord();
    $instance = (new NotifierNotifier())->discord();

    expect($facade)->toEqual($instance);
    expect($facade)->toBeInstanceOf(NotifierNotifier::class);
});

it('can use discord', function () {
    $notifier = Notifier::discord()
        ->message('Hello, Discord!')
        ->user('Notifier', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::discord()
        ->rich('Rich simple')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::discord()
        ->rich('Rich advanced')
        ->title('Notifier')
        ->user('Notifier', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->url('https://ewilan-riviere.com')
        ->author('Author', 'https://ewilan-riviere.com', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->colorSuccess()
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
                'color' => 2278750,
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

// it('can use slack', function () {
//     $notifier = Notifier::slack()->message('Hello, Slack!');
//     expect($notifier->send())->toBeTrue();
// });

// it('can use mail', function () {
//     $notifier = Notifier::mail()
//         ->subject('Hello, Mail!')
//         ->message('Hello, Mail!')
//         ->mailer(config('notifier.mail.mailer'))
//         ->host(config('notifier.mail.host'))
//         ->port(config('notifier.mail.port'))
//         ->credentials(config('notifier.mail.username'), config('notifier.mail.password'))
//         ->encryption(config('notifier.mail.encryption'))
//         ->from(config('notifier.mail.from.address'), config('notifier.mail.from.name'))
//         ->to(config('notifier.mail.to.address'), config('notifier.mail.to.name'));

//     expect($notifier->send())->toBeTrue();
// });

// it('can use custom mail', function () {
//     $notifier = Notifier::mail()
//         ->subject('Hello, Mail!')
//         ->message('Hello, Mail!');

//     expect($notifier->send())->toBeTrue();
// });

// it('can use command', function () {
//     $success = Artisan::call('notifier', [
//         'message' => 'Hello, Mail!',
//         '--type' => 'mail',
//     ]);

//     expect($success)->toBe(0);

//     $success = Artisan::call('notifier', [
//         'message' => 'Hello, Discord!',
//         '--type' => 'discord',
//         '--webhook' => config('notifier.discord.webhook'),
//     ]);

//     expect($success)->toBe(0);

//     $success = Artisan::call('notifier', [
//         'message' => 'Hello, Discord!',
//         '--type' => 'discord',
//     ]);

//     expect($success)->toBe(0);

//     $success = Artisan::call('notifier', [
//         'message' => 'Hello, Slack!',
//         '--type' => 'slack',
//         '--webhook' => config('notifier.slack.webhook'),
//     ]);

//     expect($success)->toBe(0);

//     $success = Artisan::call('notifier', [
//         'message' => 'Hello, Slack!',
//         '--type' => 'slack',
//     ]);

//     expect($success)->toBe(0);
// });
