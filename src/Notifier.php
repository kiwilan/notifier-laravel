<?php

namespace Kiwilan\LaravelNotifier;

use Kiwilan\LaravelNotifier\Facades\Journal;
use Kiwilan\Notifier\NotifierDiscord;
use Kiwilan\Notifier\NotifierHttp;
use Kiwilan\Notifier\NotifierMail;
use Kiwilan\Notifier\NotifierSlack;
use Symfony\Component\Mime\Address;

/**
 * Send notifications to email, Slack or Discord.
 */
class Notifier
{
    /**
     * Send notification to email.
     */
    public function mail(): NotifierMail
    {
        return NotifierMail::make()
            ->autoConfig($this->autoMail())
            ->logSending(function (array $data) {
                $this->logSending('mail', $data);
            })
            ->logError(function (string $reason, array $data = []) {
                $this->logError('mail', $reason, $data);
            })
            ->logSent(function (array $data) {
                $this->logSent('mail', $data);
            });
    }

    /**
     * Send notification to Slack channel via webhook.
     *
     * @param  string  $webhook  Slack webhook URL, like `https://hooks.slack.com/services/X/Y/Z`
     *
     * @see https://api.slack.com/messaging/webhooks
     */
    public function slack(?string $webhook = null, ?string $client = null): NotifierSlack
    {
        if (! $webhook) {
            $webhook = config('notifier.slack.webhook');
        }

        return NotifierSlack::make($webhook, $this->setClient($client))
            ->logError(function (string $reason, array $data = []) {
                $this->logError('slack', $reason, $data);
            })
            ->logSent(function (array $data) {
                $this->logSent('slack', $data);
            });
    }

    /**
     * Send notification to Discord channel via webhook.
     *
     * @param  string  $webhook  Discord webhook URL, like `https://discord.com/api/webhooks/X/Y`
     *
     * @see https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks
     */
    public function discord(?string $webhook = null, ?string $client = null): NotifierDiscord
    {
        if (! $webhook) {
            $webhook = config('notifier.discord.webhook');
        }

        return NotifierDiscord::make($webhook, $this->setClient($client))
            ->logError(function (string $reason, array $data = []) {
                $this->logError('discord', $reason, $data);
            })
            ->logSent(function (array $data) {
                $this->logSent('discord', $data);
            });
    }

    /**
     * Send notification to HTTP endpoint.
     *
     * @param  string  $url  HTTP endpoint URL
     */
    public function http(?string $url = null, ?string $client = null): NotifierHttp
    {
        if (! $url) {
            $url = config('notifier.http.url');
        }

        if (! $url) {
            throw new \Exception('Notifier: HTTP URL is not set');
        }

        return NotifierHttp::make($url, $this->setClient($client))
            ->logError(function (string $reason, array $data = []) {
                $this->logError('http', $reason, $data);
            })
            ->logSent(function (array $data) {
                $this->logSent('http', $data);
            });
    }

    public function isDebug(): bool
    {
        $dotenv = config('notifier.journal.debug');

        if ($dotenv === 'true' || $dotenv === true) {
            return true;
        }

        return false;
    }

    protected function logSending(string $type, array $data = []): void
    {
        if ($this->isDebug()) {
            Journal::debug("Notifier for {$type} sending", $data);
        }
    }

    protected function logError(string $type, string $reason, array $data = []): void
    {
        Journal::error("Notifier for {$type} failed: {$reason}", $data);
    }

    protected function logSent(string $type, array $data = []): void
    {
        if ($this->isDebug()) {
            Journal::debug("Notifier for {$type} sent", $data);
        }
    }

    private function setClient(?string $client = null): string
    {
        if (! $client) {
            $client = config('notifier.client', 'stream');
        }

        return $client;
    }

    /**
     * Use default mailer from `.env` file.
     */
    private function autoMail(): array
    {
        $laravel_override = config('notifier.mail.laravel_override');
        $mailer = $laravel_override ? config('mail.mailer') : config('notifier.mail.mailer');
        $host = $laravel_override ? config('mail.host') : config('notifier.mail.host');
        $port = $laravel_override ? config('mail.port') : config('notifier.mail.port');
        $encryption = $laravel_override ? config('mail.encryption') : config('notifier.mail.encryption');
        $username = $laravel_override ? config('mail.username') : config('notifier.mail.username');
        $password = $laravel_override ? config('mail.password') : config('notifier.mail.password');
        $from = $laravel_override
            ? new Address(config('mail.from.address'), config('mail.from.name'))
            : new Address(config('notifier.mail.from.address'), config('notifier.mail.from.name'));
        $to = [new Address(config('notifier.mail.to.address'), config('notifier.mail.to.name'))];
        $subject = config('notifier.mail.subject');

        return [
            'mailer' => $mailer,
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $username,
            'password' => $password,
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
        ];
    }
}
