<?php

namespace Kiwilan\Notifier\Notifier\Discord;

class NotifierDiscordMessage extends NotifierDiscordContainer
{
    protected function __construct(
        protected ?string $message = null,
        protected ?string $username = null,
        protected ?string $avatarUrl = null,
    ) {
    }

    public static function create(string $webhook, string $message): self
    {
        if (strlen($message) > 2000) {
            $message = substr($message, 0, 1980).'...';
        }

        $self = new self($message);
        $self->webhook = $webhook;

        return $self;
    }

    public function user(string $username, ?string $avatarUrl = null): self
    {
        $this->username = $username;

        if ($avatarUrl) {
            $this->avatarUrl = $avatarUrl;
        }

        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->username) {
            $data['username'] = $this->username;
        }

        if ($this->avatarUrl) {
            $data['avatar_url'] = $this->avatarUrl;
        }

        $data['content'] = $this->message ?? 'Empty message.';

        return $data;
    }
}
