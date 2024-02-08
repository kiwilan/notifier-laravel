<?php

namespace Kiwilan\Notifier\Notifier;

use Kiwilan\Notifier\Notifier;
use Kiwilan\Notifier\Notifier\Slack\NotifierSlackMessage;

/**
 * @see https://api.slack.com/messaging/webhooks#advanced_message_formatting
 * @see https://api.slack.com/block-kit
 */
class NotifierSlack extends Notifier
{
    protected function __construct(
        protected string $webhook,
        protected ?string $message = null,
    ) {
    }

    public static function make(string $webhook): self
    {
        return new self($webhook);
    }

    /**
     * @param  string[]|string  $message
     */
    public function message(array|string $message): NotifierSlackMessage
    {
        $message = $this->arrayToString($message);

        return NotifierSlackMessage::create($this->webhook, $message);
    }
}
