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
    protected function __construct(
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
    public static function slack(string $webhook): NotifierSlack
    {
        $self = new self();
        $self->type = 'slack';

        return NotifierSlack::make($webhook);
    }

    /**
     * Send notification to Discord channel via webhook.
     *
     * @param  string  $webhook  Discord webhook URL, like `https://discord.com/api/webhooks/X/Y`
     *
     * @see https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks
     */
    public static function discord(string $webhook): NotifierDiscord
    {
        $self = new self();
        $self->type = 'discord';

        return NotifierDiscord::make($webhook);
    }

    protected function logSending(string $message): void
    {
        if (config('app.debug') === true) {
            Log::debug("Notifier: sending {$this->type} notification: {$message}...");
        }
    }

    protected function logError(string $reason): void
    {
        Log::error("Notifier: notification failed: {$reason}");
    }

    protected function logSent(): void
    {
        if (config('app.debug') === true) {
            Log::debug("Notifier: {$this->type} notification sent");
        }
    }

    /**
     * @param  string|string[]  $array
     */
    protected function arrayToString(array|string $array): string
    {
        return implode(PHP_EOL, $array);
    }

    protected function sendRequest(
        string $url,
        array $headers = [
            'Accept' => 'application/json',
        ],
        array $bodyJson = [],
    ): \Psr\Http\Message\ResponseInterface {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'json' => $bodyJson,
            'http_errors' => false,
        ]);

        return $response;
    }
}
