<?php

use Illuminate\Support\Facades\Config;
use Kiwilan\LaravelNotifier\Facades\Notifier;

beforeEach(function () {
    Config::set('notifier.journal.debug', dotenv()['NOTIFIER_JOURNAL_DEBUG']);
});

it('can use', function () {
    $url = 'https://jsonplaceholder.typicode.com/posts';
    $http = Notifier::http($url)->send();
    expect($http->getRequest()->getStatusCode())->toBe(200);

    $url = 'https://jsonplaceholder.typicode.com/post';
    $http = Notifier::http($url)->send();
    expect($http->getRequest()->getStatusCode())->toBe(500);
});
