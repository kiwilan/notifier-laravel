<?php

use Kiwilan\Notifier\Facades\Journal;

beforeEach(function () {
});

it('can use journal', function () {
    $journal = Journal::info('Hello, Journal!');
    $log = getLog();

    dump($journal);

    $exception = new Exception('Hello, Exception!');
    $journal = Journal::handler($exception);
});
