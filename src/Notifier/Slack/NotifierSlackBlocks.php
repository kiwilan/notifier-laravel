<?php

namespace Kiwilan\Notifier\Notifier\Slack;

use Kiwilan\Notifier\Utils\NotifierHelpers;

class NotifierSlackBlocks extends NotifierSlackContainer
{
    protected function __construct(
        protected ?string $text = null,
    ) {
    }

    public static function create(string $webhook, string $message): self
    {
        $message = NotifierHelpers::truncate($message);

        $self = new self($message);
        $self->webhook = $webhook;

        return $self;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
        ];
    }
}
