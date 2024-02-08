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

    /**
     * @param  string[]|string  $message
     */
    public function message(array|string $message): NotifierDiscordMessage
    {
        $message = $this->arrayToString($message);

        return NotifierDiscordMessage::create($this->webhook, $message);
    }

    /**
     * @param  string[]|string  $message
     */
    public function rich(array|string $message): NotifierDiscordRich
    {
        $message = $this->arrayToString($message);

        return NotifierDiscordRich::create($this->webhook, $message);
    }
}
