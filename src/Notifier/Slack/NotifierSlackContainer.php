<?php

namespace Kiwilan\Notifier\Notifier\Slack;

use Illuminate\Support\Facades\Log;
use Kiwilan\Notifier\Utils\NotifierRequest;

abstract class NotifierSlackContainer
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

        $this->isSuccess = $this->request->getStatusCode() === 200;

        if ($this->isSuccess) {
            Log::error("Notifier: slack notification failed with HTTP {$this->request->getStatusCode()}", [
                $this->request->toArray(),
            ]);
        }

        return $this;
    }
}
