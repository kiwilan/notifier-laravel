<?php

namespace Kiwilan\Notifier\Notifier\Discord;

use Kiwilan\Notifier\Utils\NotifierRequest;

abstract class NotifierDiscordContainer
{
    protected function __construct(
        protected ?string $webhook = null,
        protected ?NotifierRequest $request = null,
        protected bool $isSuccess = false,
    ) {
    }

    abstract public static function create(string $webhook, string $description): self;

    abstract public function toArray(): array;

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function send(): static
    {
        $this->request = NotifierRequest::make($this->webhook)
            ->requestData($this->toArray())
            ->send();

        $this->isSuccess = $this->request->getStatusCode() === 204;

        return $this;
    }
}
