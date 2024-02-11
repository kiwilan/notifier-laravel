<?php

namespace Kiwilan\LaravelNotifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\LaravelNotifier\Journal
 *
 * @method static \Kiwilan\LaravelNotifier\Journal info(string $message, array|string $data = [])
 * @method static \Kiwilan\LaravelNotifier\Journal debug(string $message, array|string $data = [])
 * @method static \Kiwilan\LaravelNotifier\Journal warning(string $message, array|string $data = [])
 * @method static \Kiwilan\LaravelNotifier\Journal error(string $message, array|string $data = [])
 * @method static \Kiwilan\LaravelNotifier\Journal handler(\Throwable $e, bool $toDatabase = false, ?string $toNotifier = null)
 */
class Journal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\LaravelNotifier\Journal::class;
    }
}
