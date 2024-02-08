<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\Notifier\Facades\Notifier;
use Kiwilan\Notifier\Notifier as NotifierNotifier;

beforeEach(function () {
    Config::set('notifier.slack.webhook', dotenv()['NOTIFIER_SLACK_WEBHOOK']);
});

it('can use instance', function () {
    $facade = Notifier::slack();
    $instance = (new NotifierNotifier())->slack();

    expect($facade)->toEqual($instance);
    expect($facade)->toBeInstanceOf(NotifierNotifier::class);
});

it('can use', function () {
    $notifier = Notifier::slack()
        ->message('Hello, Slack!')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::slack()
        ->message([
            'Hello',
            'Slack!',
        ])
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::slack()
        ->attachment('*Hello, Slack!*')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();

    $notifier = Notifier::slack()
        ->blocks('*Hello, Slack!*')
        ->send();
    expect($notifier->isSuccess())->toBeTrue();
});

it('can use command', function () {
    $success = Artisan::call('notifier', [
        'message' => 'Hello, Slack!',
        '--type' => 'slack',
        '--webhook' => config('notifier.slack.webhook'),
    ]);

    expect($success)->toBe(0);

    $success = Artisan::call('notifier', [
        'message' => 'Hello, Slack!',
        '--type' => 'slack',
    ]);

    expect($success)->toBe(0);
});
