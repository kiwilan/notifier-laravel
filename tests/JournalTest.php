<?php

use Kiwilan\Notifier\Facades\Journal;

beforeEach(function () {
});

it('can use journal', function () {
    $journal = Journal::info('Hello, Journal!');
    $log = getLog();
});
