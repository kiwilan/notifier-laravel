<?php

namespace Kiwilan\Notifier;

use Illuminate\Support\Facades\Log;
use Kiwilan\Notifier\Notifier\NotifierDiscord;
use Kiwilan\Notifier\Notifier\NotifierMail;
use Kiwilan\Notifier\Notifier\NotifierSlack;
use Kiwilan\Notifier\Utils\NotifierRequest;

/**
 * Send notifications to email, Slack or Discord.
 */
class Notifier
{
    public function __construct(
        protected string $type = 'unknown',
        protected array $requestData = [],
        protected ?NotifierRequest $request = null,
    ) {
    }

    /**
     * Send notification to email.
     */
    public static function mail(): NotifierMail
    {
        $self = new self();
        $self->type = 'mail';
        $self->requestData = [];

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
}
