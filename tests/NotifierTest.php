<?php

use Illuminate\Support\Facades\Config;
use Kiwilan\Notifier\Facades\Notifier;

beforeEach(function () {
    Config::set('notifier.discord.webhook', dotenv()['NOTIFIER_DISCORD_WEBHOOK']);
    Config::set('notifier.discord.username', dotenv()['NOTIFIER_DISCORD_USERNAME']);
    Config::set('notifier.discord.avatar_url', dotenv()['NOTIFIER_DISCORD_AVATAR_URL']);
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
});

it('can use discord', function () {
    $notifier = Notifier::discord()->message('Hello, Discord!');
    expect($notifier->send())->toBeTrue();
});

it('can use slack', function () {
    $notifier = Notifier::slack()->message('Hello, Slack!');
    expect($notifier->send())->toBeTrue();
});

it('can use mail', function () {
    $notifier = Notifier::mail()
        ->subject('Hello, Mail!')
        ->message('Hello, Mail!');
    expect($notifier->send())->toBeTrue();
});
