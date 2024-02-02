<?php

namespace Kiwilan\Notifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Notifier\Journal
 */
class Journal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Notifier\Journal::class;
    }
}
