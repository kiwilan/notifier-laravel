<?php

namespace Kiwilan\Notifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Notifier\Notifier
 *
 * @method static \Kiwilan\Notifier\Notifier\NotifierMail mail()
 * @method static \Kiwilan\Notifier\Notifier\NotifierSlack slack(?string $webhook = null)
 * @method static \Kiwilan\Notifier\Notifier\NotifierDiscord discord(?string $webhook = null)
 */
class Notifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Notifier\Notifier::class;
    }
}
