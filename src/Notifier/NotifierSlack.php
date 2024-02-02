<?php

namespace Kiwilan\Notifier\Notifier;

use Kiwilan\Notifier\Notifier;

class NotifierSlack extends Notifier implements INotifier
{
    protected function __construct(
        protected string $webhook,
        protected ?string $message = null,
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
     * Prepare message for sending.
     */
    private function createBody(): self
    {
        $this->body = [
            'text' => $this->message ?? '',
        ];

        return $this;
    }

    public function send(): bool
    {
        $this->createBody();
        $res = $this->sendRequest($this->webhook, $this->body);

        if ($res['status_code'] !== 200) {
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
        ];
    }
}
