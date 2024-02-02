<?php

namespace Kiwilan\Notifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Notifier\Notifier
 */
class Notifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Notifier\Notifier::class;
    }
}
