<?php

namespace Kiwilan\Notifier;

use Illuminate\Support\Facades\Log;
use Kiwilan\Notifier\Notifier\NotifierDiscord;
use Kiwilan\Notifier\Notifier\NotifierMail;
use Kiwilan\Notifier\Notifier\NotifierSlack;

/**
 * Send notifications to email, Slack or Discord.
 */
class Notifier
{
    public function __construct(
        protected string $type = 'unknown',
    ) {
    }

    /**
     * Send notification to email.
     */
    public static function mail(): NotifierMail
    {
        $self = new self();
        $self->type = 'mail';

        return NotifierMail::make();
    }

    /**
     * Send notification to Slack channel via webhook.
     *
     * @param  string  $webhook  Slack webhook URL, like `https://hooks.slack.com/services/X/Y/Z`
     *
     * @see https://api.slack.com/messaging/webhooks
     */
    public static function slack(?string $webhook = null): NotifierSlack
    {
        $self = new self();
        $self->type = 'slack';

        if (! $webhook) {
            $webhook = config('notifier.slack.webhook');
        }

        $self->checkWebhook($webhook);

        return NotifierSlack::make($webhook);
    }

    /**
     * Send notification to Discord channel via webhook.
     *
     * @param  string  $webhook  Discord webhook URL, like `https://discord.com/api/webhooks/X/Y`
     *
     * @see https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks
     */
    public static function discord(?string $webhook = null): NotifierDiscord
    {
        $self = new self();
        $self->type = 'discord';

        if (! $webhook) {
            $webhook = config('notifier.discord.webhook');
        }

        $self->checkWebhook($webhook);

        return NotifierDiscord::make($webhook);
    }

    protected function logSending(string $message): void
    {
        if (config('app.debug') === true) {
            Log::debug("Notifier: sending {$this->type} notification: {$message}...");
        }
    }

    protected function logError(string $reason, array $data = []): void
    {
        Log::error("Notifier: notification failed: {$reason}", $data);
    }

    protected function logSent(): void
    {
        if (config('app.debug') === true) {
            Log::debug("Notifier: {$this->type} notification sent");
        }
    }

    protected function checkWebhook(?string $webhook = null): void
    {
        if (! $webhook) {
            throw new \InvalidArgumentException("Notifier: {$this->type} webhook URL is required");
        }
    }

    /**
     * @param  string|string[]  $message
     */
    protected function arrayToString(array|string $message): string
    {
        if (is_string($message)) {
            return $message;
        }

        return implode(PHP_EOL, $message);
    }

    /**
     * Send HTTP request.
     *
     * @param  string  $url  URL to send request to
     * @param  array  $body  Request body
     * @param  array  $headers  Request headers
     * @return array{status_code: int, body: string}
     */
    protected function sendRequest(
        string $url,
        array $body = [],
        array $headers = [],
        bool $json = true,
    ): array {
        $headers = [
            ...$headers,
            $json ? 'Content-type: application/json' : 'Content-type: application/x-www-form-urlencoded',
        ];

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => $headers,
                'content' => $json ? json_encode($body) : http_build_query($body),
            ],
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return [
                'status_code' => 500,
                'body' => "Failed to send request to {$url}",
            ];
        }

        $httpCode = $http_response_header[0];
        $statusCode = explode(' ', $httpCode)[1];
        $httpCode = (int) $statusCode;
        $body = $response;

        return [
            'status_code' => $httpCode,
            'body' => $body,
        ];
    }
}
