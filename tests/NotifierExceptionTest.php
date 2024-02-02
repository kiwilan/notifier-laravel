<?php

use Kiwilan\Notifier\Facades\Notifier;

it('can throw exception', function () {
    expect(fn () => Notifier::discord())->toThrow(Exception::class);
    expect(fn () => Notifier::slack())->toThrow(Exception::class);
});
