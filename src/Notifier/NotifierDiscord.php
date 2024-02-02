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

    public function send(): bool
    {
        $body = [
            'content' => $this->message ?? '',
        ];

        if ($this->username) {
            $body['username'] = $this->username;
        }

        if ($this->avatarUrl) {
            $body['avatar_url'] = $this->avatarUrl;
        }

        $res = $this->sendRequest($this->webhook, bodyJson: $body);

        if ($res->getStatusCode() !== 204) {
            $this->logError("status code {$res->getStatusCode()}, {$res->getBody()->getContents()}");

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
