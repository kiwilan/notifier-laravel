<?php

namespace Kiwilan\Notifier\Notifier;

use Kiwilan\Notifier\Notifier;
use Kiwilan\Notifier\Utils\NotifierRequest;

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

    public function message(array|string $message): self
    {
        $this->message = $this->arrayToString($message);

        return $this;
    }

    public function send(): bool
    {
        $this->request = NotifierRequest::make($this->webhook)
            ->requestData($this->toArray())
            ->send();

        if ($this->request->getStatusCode() !== 200) {
            $this->logError("status code {$this->request->getStatusCode()}", [
                $this->request->toArray(),
            ]);

            return false;
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->message ?? '',
        ];
    }
}
