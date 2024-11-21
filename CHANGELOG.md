# Changelog

All notable changes to `notifier-laravel` will be documented in this file.

## v0.3.18 - 2024-09-26

Fix notifier handler with null webhook

## v0.3.17 - 2024-05-02

Update `kiwilan/php-notifier`

## v0.3.16 - 2024-03-16

Update dependencies

## v0.3.15 - 2024-03-16

Add support for Laravel 11

## v0.3.14 - 2024-02-24

`Journal` with `toDatabase()` fixes

- `notifier.to_database.recipients_id` have to be filled to send automatic notifications to the database.
- add icon with level of severity to the notification.
- set manual recipients is possible.

## v0.3.13 - 2024-02-16

`Journal` improve `handler()`

## v0.3.12 - 2024-02-15

Hotfix

## v0.3.11 - 2024-02-14

- Fix Journal empty message

## v0.3.10 - 2024-02-14

- add improvements for Journal with logs

## v0.3.0 - 2024-02-14

Fixes

## v0.2.0 - 2024-02-11

Import `kiwilan/php-notifier` to replace current `Notifier`

## v0.1.16 - 2024-02-05

Hotfix for `Notifier` remove urlencode

## v0.1.15 - 2024-02-05

`Notifier` add `urlencode()` to clean URL and try/catch on sending request to avoid error.

## v0.1.14 - 2024-02-05

`NotifierCommand` hotfix.

## v0.1.13 - 2024-02-05

`NotifierCommand::class` fix `webhook` option.

## v0.1.12 - 2024-02-02

Typo fix

## v0.1.11 - 2024-02-02

`Journal::class` param `data` can be `string`.

## v0.1.10 - 2024-02-02

Add more tests

## v0.1.0 - 2024-02-02

init
