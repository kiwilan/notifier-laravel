# notifier-laravel

Notifier for Laravel is a package to add some useful classes to send notifications and logging.

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
