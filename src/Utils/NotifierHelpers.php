<?php

namespace Kiwilan\Notifier\Utils;

class NotifierHelpers
{
    public static function truncate(?string $string, int $length = 2000): ?string
    {
        if (! $string) {
            return null;
        }

        if (strlen($string) > $length) {
            $string = substr($string, 0, $length - 20).'...';
        }

        return $string;
    }
}
