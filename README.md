# **Notifier for Laravel**

![Banner with british letter box picture in background and Notifier for Laravel title](https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg)

[![php][php-version-src]][php-version-href]
[![laravel][laravel-src]][laravel-href]

[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]
[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

> [!WARNING]
> Not ready for production for now.

Notifier for Laravel is a package to add some useful classes to send notifications (with `Notifier::class`) and monitoring (with `Journal::class`).

Works for [Discord webhooks](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks), [Slack webhooks](https://api.slack.com/messaging/webhooks) and emails with [`symfony/mailer`](https://symfony.com/doc/current/mailer.html).

> [!NOTE]
> Laravel offers a built-in [Notification](https://laravel.com/docs/10.x/notifications) system, but this package is an alternative to it. Current package offer a simple way to send notifications without link to a user model and advanced way to monitoring (can be linked ton `filament/notifications` package (not included and not required).
>
> When native Laravel notifications are for users, this package is designed for developers to help for debugging and monitoring, but you can use it for users too.

> [!IMPORTANT]
> This package offer a support for Discord and Slack webhooks, but Slack has only basic support, for more, you can use [`laravel/slack-notification-channel`](https://github.com/laravel/slack-notification-channel). To avoid dependencies, this package doesn't use it.

## Installation

You can install the package via composer:

```bash
composer require kiwilan/notifier-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="notifier-config"
```

This is the contents of the published config file:

```php
return [
    'discord' => [
        // Default Discord webhook URL.
        'webhook' => env('NOTIFIER_DISCORD_WEBHOOK', null),
        // Default Discord username.
        'username' => env('NOTIFIER_DISCORD_USERNAME', null),
        // Default Discord avatar URL.
        'avatar_url' => env('NOTIFIER_DISCORD_AVATAR_URL', null),
    ],

    'mail' => [
        // Use Laravel mailer instead package from `.env` file.
        'laravel_override' => env('NOTIFIER_MAIL_LARAVEL_OVERRIDE', false),
        // Set default subject for mail.
        'subject' => env('NOTIFIER_MAIL_SUBJECT', 'Notifier'),
        // Set default mailer from `.env` file.
        'mailer' => env('NOTIFIER_MAIL_MAILER', 'smtp'),
        'host' => env('NOTIFIER_MAIL_HOST', 'mailpit'),
        'port' => env('NOTIFIER_MAIL_PORT', 1025),
        'username' => env('NOTIFIER_MAIL_USERNAME', null),
        'password' => env('NOTIFIER_MAIL_PASSWORD', null),
        'encryption' => env('NOTIFIER_MAIL_ENCRYPTION', 'tls'),
        'from_address' => env('NOTIFIER_MAIL_FROM_ADDRESS', null),
        'from_name' => env('NOTIFIER_MAIL_FROM_NAME', null),
        'to_address' => env('NOTIFIER_MAIL_TO_ADDRESS', null),
        'to_name' => env('NOTIFIER_MAIL_TO_NAME', null),
    ],

    'slack' => [
        // Default Slack webhook URL.
        'webhook' => env('NOTIFIER_SLACK_WEBHOOK', null),
    ],

    // This feature use `filament/notifications` package, not included in this package.
    'to_database' => [
        // Default user model for notification.
        'model' => env('NOTIFIER_TO_DATABASE_USER', 'App\Models\User'),
        // Recipients ID for notification.
        'recipients_id' => explode(',', env('NOTIFIER_TO_DATABASE_RECIPIENTS_ID', ''), 0),
    ],
];
```

## Usage

### Journal

Journal is a utility class for [Laravel Logging](https://laravel.com/docs/10.x/logging).

```php
use Kiwilan\Notifier\Facades\Journal;

Journal::debug('Hello, Journal!');
Journal::info('Hello, Journal!');
Journal::warning('Hello, Journal!');
Journal::error('Hello, Journal!');
```

#### To database

You can use Journal to log in the database with `filament/notifications` package (you have to install it).

This method will search `App\Models\User::class` and get all users with `canAccessPanel()` allowed, by default all users with access will be notified.

```php
use Kiwilan\Notifier\Facades\Journal;

Journal::info('Hello, Journal!')
  ->toDatabase();
```

#### To notifier

You can use Journal to send a notification with `discord`, `mail` or `slack` (you have to set the config file).

```php
use Kiwilan\Notifier\Facades\Journal;

Journal::info('Hello, Journal!')
  ->toNotifier('discord');
```

#### Handler

You can use Journal as a handler for [Laravel Exceptions](https://laravel.com/docs/10.x/errors).

-   `toDatabase` is a boolean to log the exception in the database with `filament/notifications` package (you have to install it).
-   `toNotifier` is a string to send a notification with `discord`, `mail` or `slack` (you have to set the config file).

```php
<?php

namespace App\Exceptions;

use Kiwilan\Notifier\Facades\Journal;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
  public function register(): void
  {
    $this->reportable(function (Throwable $e) {
      Journal::handler($e, toDatabase: true, toNotifier: 'mail');
    });
  }
}
```

### Notifier

Notifier is an alternative to [Laravel Notifications](https://laravel.com/docs/10.x/notifications).

> [!NOTE]
> If `app.debug` is `true`, `debug` level logs will be written for sending and sent notifications.
> In all cases, `error` level logs will be written for sending errors.

#### Discord

Default webhook URL, username and avatar URL can be set in the config file.

```php
use Kiwilan\Notifier\Facades\Notifier;

$notifier = Notifier::discord()
  ->username('Laravel')
  ->avatarUrl('https://laravel.com/img/favicon/favicon-32x32.png')
  ->message('Hello, Discord!');

$notifier->send();
```

You can pass a custom webhook URL:

```php
use Kiwilan\Notifier\Facades\Notifier;

$notifier = Notifier::discord('https://discord.com/api/webhooks/1234567890/ABCDEFGHIJKLMN0123456789');
```

#### Mail

Default `mailer`, `host`, `port`, `username`, `password`, `encryption`, `from address`, `from name`, `to address` and `to name` can be set in the config file.

You can use `NOTIFIER_MAIL_LARAVEL_OVERRIDE` to use Laravel mailer instead of package mailer.

```php
use Kiwilan\Notifier\Facades\Notifier;

$notifier = Notifier::mail()
  ->subject('Hello, Mail!')
  ->message('Hello, Mail!');

$notifier->send();
```

You can pass a custom mailer:

```php
use Kiwilan\Notifier\Facades\Notifier;

$notifier = Notifier::mail('smtp')
  ->from('hello@example.com', 'Hello')
  ->to('to@example.com', 'To')
  ->subject('Hello, Mail!')
  ->message('Hello, Mail!')
  ->mailer('smtp')
  ->host('mailpit')
  ->port(1025)
  ->username(null)
  ->password(null)
  ->encryption('tls');
```

#### Slack

Default webhook URL can be set in the config file.

```php
use Kiwilan\Notifier\Facades\Notifier;

$notifier = Notifier::slack()
  ->message('Hello, Slack!');

$notifier->send();
```

You can pass a custom webhook URL:

```php
use Kiwilan\Notifier\Facades\Notifier;

$notifier = Notifier::slack('https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX');
```

#### Command

You can use Notifier as a command to send a notification with `discord`, `mail` or `slack`.

Two options are available:

-   `-t` or `--type` to set the type of notification, default is `mail`.
-   `-w` or `--webhook` to set the webhook URL (only for `discord` and `slack`). If not set, the default webhook URL from the config file will be used.

```bash
php artisan notifier -t=discord -w=https://discord.com/api/webhooks/1234567890/ABCDEFGHIJKLMN0123456789 "Hello, Discord!"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Ewilan Rivière](https://github.com/ewilan-riviere)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[<img src="https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg" height="120rem" width="100%" />](https://github.com/kiwilan)

[version-src]: https://img.shields.io/packagist/v/kiwilan/notifier-laravel.svg?style=flat-square&colorA=18181B&colorB=777BB4
[version-href]: https://packagist.org/packages/kiwilan/notifier-laravel
[php-version-src]: https://img.shields.io/static/v1?style=flat-square&label=PHP&message=v8.1&color=777BB4&logo=php&logoColor=ffffff&labelColor=18181b
[php-version-href]: https://www.php.net/
[downloads-src]: https://img.shields.io/packagist/dt/kiwilan/notifier-laravel.svg?style=flat-square&colorA=18181B&colorB=777BB4
[downloads-href]: https://packagist.org/packages/kiwilan/notifier-laravel
[license-src]: https://img.shields.io/github/license/kiwilan/notifier-laravel.svg?style=flat-square&colorA=18181B&colorB=777BB4
[license-href]: https://github.com/kiwilan/notifier-laravel/blob/main/README.md
[tests-src]: https://img.shields.io/github/actions/workflow/status/kiwilan/notifier-laravel/run-tests.yml?branch=main&label=tests&style=flat-square&colorA=18181B
[tests-href]: https://github.com/kiwilan/notifier-laravel/actions/workflows/run-tests.yml
[codecov-src]: https://codecov.io/gh/kiwilan/notifier-laravel/branch/main/graph/badge.svg?token=1py1fk6vwc
[codecov-href]: https://codecov.io/gh/kiwilan/notifier-laravel
[laravel-src]: https://img.shields.io/static/v1?label=Laravel&message=v10&style=flat-square&colorA=18181B&colorB=FF2D20
[laravel-href]: https://laravel.com
