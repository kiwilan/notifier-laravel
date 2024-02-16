<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\LaravelNotifier\Facades\Notifier;

beforeEach(function () {
    Config::set('notifier.slack.webhook', dotenv()['NOTIFIER_SLACK_WEBHOOK']);
});

it('can use', function () {
    $notifier = Notifier::slack()
        ->message('Hello, Slack!')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::slack()
        ->message([
            'Hello',
            'array!',
        ])
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::slack()
        ->attachment('*Hello, attachment!*')
        ->colorSuccess()
        ->author('Author', 'https://github.com', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->imageUrl('https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->title('Title', 'https://github.com')
        ->fields([
            [
                'name' => 'Field 1',
                'value' => 'Value 1',
                'short' => true,
            ],
            [
                'name' => 'Field 2',
                'value' => 'Value 2',
                'short' => true,
            ],
        ])
        ->timestamp(now())
        ->footer('Footer', 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::slack()
        ->blocks('*Hello, blocks!*')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();
});

it('can use command', function () {
    $success = Artisan::call('notifier', [
        'message' => 'Hello, Slack with webhook!',
        '--type' => 'slack',
        '--webhook' => config('notifier.slack.webhook'),
    ]);
    expect($success)->toBe(0);

    $success = Artisan::call('notifier', [
        'message' => 'Hello, Slack with config!',
        '--type' => 'slack',
    ]);
    expect($success)->toBe(0);
});
