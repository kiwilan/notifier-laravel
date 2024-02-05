<?php

namespace Kiwilan\Notifier\Commands;

use Illuminate\Console\Command;
use Kiwilan\Notifier\Facades\Notifier;

class NotifierCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifier
                            {message : Message to send.}
                            {--t|type= : `mail`, `slack` or `discord`.}
                            {--w|webhook= : Webhook URL for Slack or Discor (leave blank to take config).}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications with mail, Discord or Slack, default to mail.';

    public function __construct(
        protected ?string $message = null,
        protected ?string $type = 'mail',
        protected ?string $webhook = null,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->alert('Notifier');
        $this->warn($this->description);
        $this->newLine();

        $this->message = (string) $this->argument('message');
        $this->type = (string) $this->option('type');
        $this->webhook = (string) $this->option('webhook');

        $this->info("Sending notification to {$this->type}...");

        if ($this->type === 'mail') {
            Notifier::mail()
                ->message($this->message)
                ->send();

            return Command::SUCCESS;
        }

        if ($this->type === 'discord') {
            $this->info("Webhook: {$this->webhook}");
            Notifier::discord($this->webhook)
                ->message($this->message)
                ->send();

            return Command::SUCCESS;
        }

        if ($this->type === 'slack') {
            $this->info("Webhook: {$this->webhook}");
            Notifier::slack($this->webhook)
                ->message($this->message)
                ->send();

            return Command::SUCCESS;
        }

        $this->error('Type not found.');

        return Command::FAILURE;
    }
}
