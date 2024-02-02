# **Notifier for Laravel**

![Banner with british letter box picture in background and Notifier for Laravel title](https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg)

[![php][php-version-src]][php-version-href]
[![laravel][laravel-src]][laravel-href]

[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]
[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

Notifier for Laravel is a package to add some useful classes to send notifications and logging.

Works for [Discord webhooks](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks), [Slack webhooks](https://api.slack.com/messaging/webhooks) and emails with [symfony/mailer](https://symfony.com/doc/current/mailer.html).

> [!NOTE]
> This package is an alternative to [Laravel Notifications](https://laravel.com/docs/10.x/notifications).

## Installation

You can install the package via composer:

```bash
composer require kiwilan/notifier-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="notifier-laravel-config"
```

This is the contents of the published config file:

```php
return [
  'discord' => [
    'webhook' => env('NOTIFIER_DISCORD_WEBHOOK', null),
    'username' => env('NOTIFIER_DISCORD_USERNAME', null),
    'avatar_url' => env('NOTIFIER_DISCORD_AVATAR_URL', null),
  ],

  'slack' => [
    'webhook' => env('NOTIFIER_SLACK_WEBHOOK', null),
  ],

  'mail' => [
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
];
```

## Usage

```php
$notifier = new Kiwilan\Notifier();
echo $notifier->echoPhrase('Hello, Kiwilan!');
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
[laravel-src]: https://img.shields.io/static/v1?label=Laravel&message=v9&style=flat-square&colorA=18181B&colorB=FF2D20
[laravel-href]: https://laravel.com
