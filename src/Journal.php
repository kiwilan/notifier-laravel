<?php

namespace Kiwilan\Notifier;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kiwilan\Notifier\Notifier\INotifier;

class Journal
{
    public function __construct(
        protected ?string $message = null,
        protected string $level = 'info',
        protected array $data = [],
        protected ?Collection $users = null,
        protected ?INotifier $notifier = null,
    ) {
        $this->log();
    }

    public function info(string $message, array $data = []): self
    {

        return new self($message, 'info', $data);
    }

    public function debug(string $message, array $data = []): self
    {
        return new self($message, 'debug', $data);
    }

    public function warning(string $message, array $data = []): self
    {
        return new self($message, 'warning', $data);
    }

    public function error(string $message, array $data = []): self
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
        $data = json_encode($this->data, JSON_PRETTY_PRINT);
        error_log("Journal: {$this->level} - {$this->message} - {$data}");
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
            $filamentUsers = $this->users;

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
        $this->notifier = match ($type) {
            'mail' => Notifier::mail(),
            'slack' => Notifier::slack(),
            'discord' => Notifier::discord(),
            default => null,
        };

        $this->notifier->message([$this->message, json_encode($this->data, JSON_PRETTY_PRINT)])
            ->send();

        return $this;
    }
}
