<?php

namespace Kiwilan\Notifier;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kiwilan\Notifier\Notifier\INotifier;

class Journal
{
    protected function __construct(
        protected ?string $message = null,
        protected string $level = 'info',
        protected array $data = [],
        protected ?Collection $users = null,
        protected ?INotifier $notifier = null,
    ) {
        $this->log();
    }

    public static function info(string $message, array $data = []): self
    {

        return new self($message, 'info', $data);
    }

    public static function debug(string $message, array $data = []): self
    {
        return new self($message, 'debug', $data);
    }

    public static function warning(string $message, array $data = []): self
    {
        return new self($message, 'warning', $data);
    }

    public static function error(string $message, array $data = []): self
    {
        return new self($message, 'error', $data);
    }

    /**
     * Handle exception, log as error and send notification to database.
     *
     * In `local` environment, it will return null, to not send notification to database.
     */
    public static function handler(\Throwable $e): ?self
    {
        if (config('app.env') === 'local') {
            return null;
        }

        return new self($e->getMessage(), 'error', [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    private function log(): void
    {
        Log::log($this->level, $this->message, $this->data);
    }

    /**
     * Send notification to database for Users with access to Filament admin panel with `filament/notifications` package.
     *
     * @param  Model|Authenticatable|Collection|array|null  $users  To send notification to.
     */
    public function toDatabase(Model|Authenticatable|Collection|array|null $users = null): self
    {
        if (! class_exists('\Filament\Notifications\Notification')) {
            Log::warning('Journal: Filament notifications is not installed, check https://filamentphp.com/docs/3.x/notifications/installation');

            return $this;
        }

        if (! class_exists('\App\Models\User')) {
            Log::warning('Journal: Filament notifications is installed, but User model is not found, check https://filamentphp.com/docs/3.x/notifications/installation');

            return $this;
        }

        try {
            $filamentUsers = $this->users;

            if (! $filamentUsers) {
                $users = '\App\Models\User';
                $filamentUsers = $users::all()->filter(fn ($user) => $user->canAccessPanel());
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
    public function notifier(string $type): self
    {
        $this->notifier = match ($type) {
            'mail' => Notifier::mail()->auto(),
            'slack' => Notifier::slack(config('steward.slack.webhook')),
            'discord' => Notifier::discord(config('steward.discord.webhook')),
            default => null,
        };

        $this->notifier->message($this->message)
            ->send();

        return $this;
    }
}
