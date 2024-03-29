<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\LaravelNotifier\Facades\Notifier;

beforeEach(function () {
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

it('can use', function () {
    $notifier = Notifier::mail()
        ->subject('Hello, Mail!')
        ->message('Hello, Mail!')
        ->mailer(config('notifier.mail.mailer'))
        ->host(config('notifier.mail.host'))
        ->port(config('notifier.mail.port'))
        ->credentials(config('notifier.mail.username'), config('notifier.mail.password'))
        ->encryption(config('notifier.mail.encryption'))
        ->from('from@mail.com', config('notifier.mail.from.name'))
        ->to('to@mail.com', config('notifier.mail.to.name'))
        ->send();

    expect($notifier->isSuccess())->toBeTrue();
});

it('can use custom mail', function () {
    $notifier = Notifier::mail()
        ->subject('Hello, Mail!')
        ->message('Hello, Mail!')
        ->addAttachment(__DIR__.'/media/text.md', 'text.md')
        ->send();

    expect($notifier->isSuccess())->toBeTrue();
});

it('can use command', function () {
    $success = Artisan::call('notifier', [
        'message' => 'Hello, Mail!',
        '--type' => 'mail',
    ]);

    expect($success)->toBe(0);
});
