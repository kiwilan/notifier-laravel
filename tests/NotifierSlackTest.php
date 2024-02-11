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
