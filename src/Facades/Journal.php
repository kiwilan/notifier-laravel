<?php

namespace Kiwilan\Notifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Notifier\Journal
 *
 * @method static \Kiwilan\Notifier\Journal info(string $message, array $data = [])
 * @method static \Kiwilan\Notifier\Journal debug(string $message, array $data = [])
 * @method static \Kiwilan\Notifier\Journal warning(string $message, array $data = [])
 * @method static \Kiwilan\Notifier\Journal error(string $message, array $data = [])
 * @method static \Kiwilan\Notifier\Journal handler(\Throwable $e)
 */
class Journal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Notifier\Journal::class;
    }
}
