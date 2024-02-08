<?php

namespace Kiwilan\Notifier\Notifier\Slack;

use Kiwilan\Notifier\Utils\NotifierHelpers;

class NotifierSlackMessage extends NotifierSlackContainer
{
    protected function __construct(
        protected ?string $text = null,
        protected string $type = 'plain_text',
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
        $data = [];

        // $data['text'] = [
        //     'type' => $this->type,
        //     'text' => $this->text,
        // ];

        $data['text'] = $this->text;

        return $data;
    }
}
