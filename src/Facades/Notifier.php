<?php

namespace Kiwilan\LaravelNotifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Notifier\Notifier
 *
 * @method static \Kiwilan\Notifier\NotifierMail mail()
 * @method static \Kiwilan\Notifier\NotifierSlack slack(?string $webhook = null, string $client = 'stream')
 * @method static \Kiwilan\Notifier\NotifierDiscord discord(?string $webhook = null, string $client = 'stream')
 * @method static \Kiwilan\Notifier\NotifierHttp http(?string $url = null, string $client = 'stream')
 * @method static bool isDebug()
 */
class Notifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\LaravelNotifier\Notifier::class;
    }
}
