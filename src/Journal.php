<?php

namespace Kiwilan\LaravelNotifier;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kiwilan\LaravelNotifier\Facades\Notifier;

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
    public function handler(\Throwable $e, bool $toDatabase = false, ?string $toNotifier = null): ?self
    {
        if (config('app.env') === 'local') {
            return null;
        }

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

        try {
            $filamentUsers = $users;

            $recipients_id = config('notifier.to_database.recipients_id');
            if (! $filamentUsers) {
                $filamentUsers = $model_class::query()->get();

                if ($recipients_id) {
                    $filamentUsers = $filamentUsers->filter(fn ($user) => in_array($user->id, $recipients_id));
                }
            }

            \Filament\Notifications\Notification::make()
                ->title(ucfirst($this->level))
                ->body($this->message)
                ->sendToDatabase($filamentUsers);
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
        $notifier = match ($type) {
            'mail' => Notifier::mail(),
            'slack' => Notifier::slack(),
            'discord' => Notifier::discord(),
            'http' => Notifier::http(),
            default => null,
        };

        // $this->notifier->message([$this->message, json_encode($this->data, JSON_PRETTY_PRINT)])
        //     ->send();

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
