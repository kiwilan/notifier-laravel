<?php

namespace Kiwilan\LaravelNotifier;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kiwilan\LaravelNotifier\Facades\Notifier;
use Kiwilan\Notifier\Utils\NotifierShared;

class Journal
{
    /**
     * @param  string[]|string  $data  Additional data to log.
     */
    public function __construct(
        protected ?string $message = null,
        protected string $level = 'info',
        protected array|string $data = [],
    ) {
        if (is_string($data)) {
            $this->data = [$data];
        }
        if (! empty($this->message)) {
            $this->log();
        } elseif (! empty($this->data)) {
            $this->message = 'Empty message';
            $this->log();
        }
    }

    /**
     * @param  string[]|string  $data  Additional data to log.
     */
    public function info(string $message, array|string $data = []): self
    {
        return new self($message, 'info', $data);
    }

    /**
     * @param  string[]|string  $data  Additional data to log.
     */
    public function debug(string $message, array|string $data = []): self
    {
        return new self($message, 'debug', $data);
    }

    /**
     * @param  string[]|string  $data  Additional data to log.
     */
    public function warning(string $message, array|string $data = []): self
    {
        return new self($message, 'warning', $data);
    }

    /**
     * @param  string[]|string  $data  Additional data to log.
     */
    public function error(string $message, array|string $data = []): self
    {
        return new self($message, 'error', $data);
    }

    /**
     * Handle exception, log as error and send notification to database.
     *
     * In `local` environment, it will return null, to not send notification to database.
     *
     * @param  bool  $toDatabase  Send notification to database with `filament/notifications` package.
     * @param  string|null  $toNotifier  Send notification to email, Slack or Discord, use `mail`, `slack` or `discord`.
     */
    public function handler(\Throwable $e, bool $toDatabase = true, ?string $toNotifier = null): ?self
    {
        if (config('app.env') === 'local') {
            return null;
        }

        try {
            $self = new self($e->getMessage(), 'error', [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($toDatabase) {
                $self->toDatabase();
            }

            if ($toNotifier) {
                $self->toNotifier($toNotifier);
            }

            return $self;
        } catch (\Throwable $th) {
            error_log("Journal: handler error {$th->getMessage()}");

            return null;
        }
    }

    private function log(): void
    {
        Log::log($this->level, $this->message, $this->data);
        if (config('notifier.journal.use_error_log')) {
            $data = json_encode($this->data, JSON_PRETTY_PRINT);
            $message = "Journal: {$this->level} - {$this->message}";
            if ($data) {
                $message .= "\n{$data}";
            }
            error_log($message);
        }
    }

    /**
     * Send notification to database for Users with access to Filament admin panel with `filament/notifications` package.
     *
     * @param  Model|Authenticatable|Collection|array|null  $users  To send notification to. Default use `notifier.to_database.recipients_id` from config.
     */
    public function toDatabase(Model|Authenticatable|Collection|array|null $users = null): self
    {
        if (! class_exists('\Filament\Notifications\Notification')) {
            Log::warning('Journal: Filament notifications is not installed, check https://filamentphp.com/docs/3.x/notifications/installation');

            return $this;
        }

        $model_class = config('notifier.to_database.model');
        if (! class_exists('\\'.$model_class)) {
            Log::warning("Journal: Filament notifications is installed, but {$model_class} model is not found, check https://filamentphp.com/docs/3.x/notifications/installation");

            return $this;
        }

        $recipients_id = config('notifier.to_database.recipients_id');
        if (count(($recipients_id)) === 0 || count($recipients_id) === 1 && $recipients_id[0] === '') {
            $users = null;
        } else {
            $users = $model_class::whereIn('id', $recipients_id)->get();
        }

        if (! $users) {
            Log::warning('Journal: you need to set recipients for notification, check https://filamentphp.com/docs/3.x/notifications/database-notifications');

            return $this;
        }

        try {
            $notification = \Filament\Notifications\Notification::make()
                ->title(ucfirst($this->level))
                ->body($this->message);

            $color = match ($this->level) {
                'info' => 'info',
                'debug' => 'info',
                'warning' => 'warning',
                'error' => 'danger',
                default => 'info',
            };

            $notification->{$color}()
                ->sendToDatabase($users);
        } catch (\Throwable $th) {
            Log::error("Journal: {$th->getMessage()}");
        }

        return $this;
    }

    /**
     * Send notification to email, Slack or Discord.
     *
     * @param  string  $type  `mail`, `slack` or `discord`
     */
    public function toNotifier(string $type): self
    {
        $file = $this->data['file'] ?? '';
        $file = str_replace(base_path(), '', $file);
        $file = str_replace('\\', '/', $file);

        $line = $this->data['line'] ?? '';

        $trace = $this->data['trace'] ?? '';
        $trace = str_replace('#', "\n#", $trace);
        $traceLimit = NotifierShared::truncate($trace, 1000);

        $username = config('notifier.discord.username') ?? '';

        if ($type === 'discord') {
            Notifier::discord(client: 'curl')
                ->rich("Error '{$this->message}' on {$file}, line {$line}")
                ->user($username)
                ->author($username, config('app.url'), config('notifier.discord.avatar_url'))
                ->title(config('app.name'))
                ->colorError()
                ->footer(config('app.url'), config('notifier.discord.avatar_url'))
                ->url(config('app.url'))
                ->fields([
                    [
                        'name' => 'Environment',
                        'value' => config('app.env'),
                    ],
                    [
                        'name' => 'Error',
                        'value' => $this->message,
                    ],
                    [
                        'name' => 'File',
                        'value' => $file,
                    ],
                    [
                        'name' => 'Line',
                        'value' => $line,
                    ],
                    [
                        'name' => 'Trace',
                        'value' => $traceLimit,
                    ],
                    [
                        'name' => 'URL',
                        'value' => request()->fullUrl(),
                    ],
                    [
                        'name' => 'Method',
                        'value' => request()->method(),
                    ],
                    [
                        'name' => 'User Agent',
                        'value' => request()->userAgent(),
                    ],
                    [
                        'name' => 'IP',
                        'value' => request()->ip(),
                    ],
                ])
                ->timestamp()
                ->send();

            return $this;
        }

        if ($type === 'slack') {
            Notifier::slack()
                ->attachment("Error '{$this->message}' on {$file}, line {$line}")
                ->author($username, config('app.url'), config('notifier.discord.avatar_url'))
                ->title(config('app.name'))
                ->colorError()
                ->footer(config('app.url'), config('notifier.discord.avatar_url'))
                ->fields([
                    [
                        'name' => 'Environment',
                        'value' => config('app.env'),
                        'short' => false,
                    ],
                    [
                        'name' => 'Error',
                        'value' => $this->message,
                        'short' => false,
                    ],
                    [
                        'name' => 'File',
                        'value' => $file,
                        'short' => false,
                    ],
                    [
                        'name' => 'Line',
                        'value' => $line,
                        'short' => false,
                    ],
                    [
                        'name' => 'Trace',
                        'value' => $traceLimit,
                        'short' => false,
                    ],
                    [
                        'name' => 'URL',
                        'value' => request()->fullUrl(),
                        'short' => false,
                    ],
                    [
                        'name' => 'Method',
                        'value' => request()->method(),
                        'short' => false,
                    ],
                    [
                        'name' => 'User Agent',
                        'value' => request()->userAgent(),
                        'short' => false,
                    ],
                    [
                        'name' => 'IP',
                        'value' => request()->ip(),
                        'short' => false,
                    ],
                ])
                ->send();

            return $this;
        }

        if ($type === 'mail') {
            $url = request()->fullUrl();
            $method = request()->method();
            $user_agent = request()->userAgent();
            $ip = request()->ip();

            Notifier::mail()
                ->html([
                    '<html>',
                    '<head>',
                    '<style>',
                    'body {',
                    'font-family: Arial, sans-serif;',
                    '}',
                    'h1 {',
                    'color: #ef4444;',
                    '}',
                    'pre {',
                    'white-space: pre-wrap;',
                    '}',
                    '</style>',
                    '</head>',
                    '<body>',
                    "<h1>Error '{$this->message}' on {$file}, line {$line}</h1>",
                    "<p>URL: {$url}</p>",
                    "<p>Method: {$method}</p>",
                    "<p>User Agent: {$user_agent}</p>",
                    "<p>IP: {$ip}</p>",
                    "<pre>{$trace}</pre>",
                    '</body>',
                    '</html>',
                ])
                ->message("Error '{$this->message}' on {$file}, line {$line}\n\nURL: {$url}\nMethod: {$method}\nUser Agent: {$user_agent}\nIP: {$ip}\n\n{$trace}")
                ->send();

            return $this;
        }

        if ($type === 'http') {
            Notifier::http(config('notifier.http.url'))
                ->body([
                    'message' => $this->message,
                    'level' => $this->level,
                    'data' => $this->data,
                ])
                ->send();

            return $this;
        }

        Journal::warning("Journal: Notifier type '{$type}' is not found");

        return $this;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'level' => $this->level,
            'data' => $this->data,
        ];
    }
}
