<?php

use Illuminate\Support\Facades\Config;
use Kiwilan\LaravelNotifier\Facades\Journal;
use Kiwilan\LaravelNotifier\Journal as NotifierJournal;

beforeEach(function () {
    Config::set('notifier.discord.webhook', dotenv()['NOTIFIER_DISCORD_WEBHOOK']);
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
    Config::set('app.env', 'testing');
});

it('can use instance', function () {
    $facade = Journal::info('Hello, Journal!');
    $instance = (new NotifierJournal)->info('Hello, Journal!');

    expect($facade)->toEqual($instance);
    expect($facade)->toBeInstanceOf(NotifierJournal::class);
});

it('can use different levels', function () {
    $debug = Journal::debug('debug', 'data');
    $info = Journal::info('info', ['data']);
    $warning = Journal::warning('warning');
    $error = Journal::error('error');

    expect($debug->toArray())->toBeArray();
    expect($info->toArray())->toBeArray();
    expect($warning->toArray())->toBeArray();
    expect($error->toArray())->toBeArray();
});

it('can send to database', function () {
    $debug = Journal::debug('debug')
        ->toDatabase();

    expect($debug->toArray())->toBeArray();
});

it('can send to notifier', function () {
    $debug = Journal::debug('debug')
        ->toNotifier('discord');

    expect($debug->toArray())->toBeArray();
});

it('can use journal', function () {
    $journal = Journal::info('Hello, Journal!');
    $log = getLog();

    expect($journal)->toBeInstanceOf(NotifierJournal::class);
});

it('can use handler', function () {
    $exception = new Exception('Hello, Exception!');
    $discord = Journal::handler($exception, toDatabase: true, toNotifier: 'discord');
    $slack = Journal::handler($exception, toDatabase: true, toNotifier: 'slack');
    $mail = Journal::handler($exception, toDatabase: true, toNotifier: 'mail');

    expect($discord)->toBeInstanceOf(NotifierJournal::class);

    Config::set('notifier.discord.webhook', '');
    $discord = Journal::handler($exception, toDatabase: true, toNotifier: 'discord');
    expect($discord)->toBeInstanceOf(NotifierJournal::class);
});
