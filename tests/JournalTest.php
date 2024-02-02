<?php

use Illuminate\Support\Facades\Config;
use Kiwilan\Notifier\Facades\Journal;
use Kiwilan\Notifier\Journal as NotifierJournal;

beforeEach(function () {
    Config::set('notifier.discord.webhook', dotenv()['NOTIFIER_DISCORD_WEBHOOK']);
});

it('can use instance', function () {

    $facade = Journal::info('Hello, Journal!');
    $instance = (new NotifierJournal())->info('Hello, Journal!');

    expect($facade)->toEqual($instance);
    expect($facade)->toBeInstanceOf(NotifierJournal::class);
});

it('can use different levels', function () {
    $debug = Journal::debug('debug');
    $info = Journal::info('info');
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
    // dump($journal);

    $exception = new Exception('Hello, Exception!');
    $journal = Journal::handler($exception);

    expect($journal)->toBeInstanceOf(NotifierJournal::class);
});
