<?php

namespace Kiwilan\Notifier\Notifier;

interface INotifier
{
    /**
     * Set message.
     *
     * @param  string|string[]  $message
     */
    public function message(array|string $message): self;

    /**
     * Send notification.
     */
    public function send(): bool;

    /**
     * Convert to array.
     */
    public function toArray(): array;
}
