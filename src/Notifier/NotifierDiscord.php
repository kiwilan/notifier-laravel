<?php

namespace Kiwilan\Notifier\Notifier;

use Kiwilan\Notifier\Notifier;

class NotifierDiscord extends Notifier implements INotifier
{
    protected function __construct(
        protected string $webhook,
        protected ?string $message = null,
        protected ?string $username = null,
        protected ?string $avatarUrl = null,
        protected array $body = [],
    ) {
    }

    public static function make(string $webhook): self
    {
        return new self($webhook);
    }

    public function message(array|string $message): self
    {
        $this->message = $this->arrayToString($message);

        return $this;
    }

    /**
     * Set username, different from default webhook username.
     */
    public function username(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set avatar url, different from default webhook avatar url.
     */
    public function avatarUrl(string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    private function createBody(): self
    {
        $this->body = [
            'content' => $this->message ?? '',
        ];

        if ($this->username) {
            $this->body['username'] = $this->username;
        }

        if ($this->avatarUrl) {
            $this->body['avatar_url'] = $this->avatarUrl;
        }

        if (config('notifier.discord.username') && ! $this->username) {
            $this->body['username'] = config('notifier.discord.username');
        }

        if (config('notifier.discord.avatar_url') && ! $this->avatarUrl) {
            $this->body['avatar_url'] = config('notifier.discord.avatar_url');
        }

        return $this;
    }

    public function send(): bool
    {
        $this->createBody();
        $res = $this->sendRequest($this->webhook, $this->body);

        if ($res['status_code'] !== 204) {
            $this->logError("status code {$res['status_code']}, {$res['body']}");

            return false;
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'webhook' => $this->webhook,
            'message' => $this->message,
            'username' => $this->username,
        ];
    }
}
