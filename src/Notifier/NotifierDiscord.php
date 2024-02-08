<?php

namespace Kiwilan\Notifier\Notifier;

use Kiwilan\Notifier\Notifier;
use Kiwilan\Notifier\Notifier\Discord\NotifierDiscordMessage;
use Kiwilan\Notifier\Notifier\Discord\NotifierDiscordRich;

/**
 * @see https://gist.github.com/Birdie0/78ee79402a4301b1faf412ab5f1cdcf9
 * @see https://birdie0.github.io/discord-webhooks-guide/
 * @see https://github.com/spatie/laravel-backup/blob/main/src/Notifications/Channels/Discord/DiscordMessage.php
 */
class NotifierDiscord extends Notifier
{
    protected function __construct(
        protected ?string $webhook = null,
    ) {
    }

    public static function make(string $webhook): self
    {
        return new self($webhook);
    }

    public function message(string $message): NotifierDiscordMessage
    {
        return NotifierDiscordMessage::create($this->webhook, $message);
    }

    public function rich(string $message): NotifierDiscordRich
    {
        return NotifierDiscordRich::create($this->webhook, $message);
    }
}
