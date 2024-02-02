<?php

namespace Kiwilan\Notifier\Notifier;

use Kiwilan\Notifier\Notifier;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class NotifierMail extends Notifier implements INotifier
{
    /**
     * @param  Address[]  $to  Array of `Address` object
     */
    protected function __construct(
        protected ?string $mailer = null,
        protected ?string $host = null,
        protected ?int $port = null,
        protected ?string $encryption = null,
        protected ?string $username = null,
        protected ?string $password = null,
        protected ?TransportInterface $mailTransport = null,
        protected ?Email $mailEmail = null,
        protected ?Mailer $mailMailer = null,
        protected array $to = [],
        protected ?Address $from = null,
        protected ?Address $replyTo = null,
        protected ?string $subject = null,
        protected ?string $message = null,
        protected ?string $html = null,
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param  string  $mailer  Mailer transport, default `smtp`
     */
    public function mailer(string $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @param  string  $host  Mailer host, default `mailpit`
     */
    public function host(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param  int  $port  Mailer port, default `1025`
     */
    public function port(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param  string  $encryption  Mailer encryption, default `tls`
     */
    public function encryption(string $encryption): self
    {
        $this->encryption = $encryption;

        return $this;
    }

    public function credentials(string $username, string $password): self
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * Use default mailer from `.env` file.
     */
    private function auto(): self
    {
        if (config('notifier.mail.laravel_override')) {
            if (! $this->mailer) {
                $this->mailer = config('mail.mailer');
            }

            if (! $this->host) {
                $this->host = config('mail.host');
            }

            if (! $this->port) {
                $this->port = config('mail.port');
            }

            if (! $this->encryption) {
                $this->encryption = config('mail.encryption');
            }

            if (! $this->username) {
                $this->username = config('mail.username');
            }

            if (! $this->password) {
                $this->password = config('mail.password');
            }

            if (! $this->from) {
                $this->from = new Address(config('mail.from.address'), config('mail.from.name'));
            }

            return $this;
        }

        if (! $this->mailer) {
            $this->mailer = config('notifier.mail.mailer');
        }

        if (! $this->host) {
            $this->host = config('notifier.mail.host');
        }

        if (! $this->port) {
            $this->port = config('notifier.mail.port');
        }

        if (! $this->encryption) {
            $this->encryption = config('notifier.mail.encryption');
        }

        if (! $this->username) {
            $this->username = config('notifier.mail.username');
        }

        if (! $this->password) {
            $this->password = config('notifier.mail.password');
        }

        if (! $this->from) {
            $this->from = new Address(config('notifier.mail.from.address'), config('notifier.mail.from.name'));
        }

        if (count($this->to) === 0) {
            $this->to = [new Address(config('notifier.mail.to.address'), config('notifier.mail.to.name'))];
        }

        if (! $this->subject) {
            $this->subject = config('notifier.mail.subject');
        }

        if (! $this->html) {
            $this->html = $this->message;
        }

        return $this;
    }

    /**
     * @param  Address[]|string  $to  Array of `Address` object
     * @param  string|null  $name  Useful if `$to` is a string
     */
    public function to(array|string $to, ?string $name = null): self
    {
        if (is_string($to)) {
            $to = [new Address($to, $name)];
        }

        $this->to = $to;

        return $this;
    }

    public function from(string $from, ?string $name = null): self
    {
        $this->from = new Address($from, $name ?? '');

        return $this;
    }

    public function replyTo(string $replyTo, ?string $name = null): self
    {
        $this->replyTo = new Address($replyTo, $name);

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function message(array|string $message): self
    {
        $this->message = $this->arrayToString($message);

        return $this;
    }

    /**
     * @param  string|string[]  $html
     */
    public function html(array|string $html): self
    {
        $this->html = $this->arrayToString($html);

        return $this;
    }

    public function send(): bool
    {
        $this->auto();
        $this->mailTransport = Transport::fromDsn("{$this->mailer}://{$this->host}:{$this->port}");
        $this->mailMailer = new Mailer($this->mailTransport);

        $this->logSending("{$this->mailer}://{$this->host}:{$this->port}");

        $this->mailEmail = (new Email())
            ->to(...$this->to)
            ->from($this->from);

        if ($this->replyTo) {
            $this->mailEmail->replyTo($this->replyTo);
        }

        if ($this->subject) {
            $this->mailEmail->subject($this->subject);
        }

        if ($this->message) {
            $this->mailEmail->text($this->message);
        }

        if ($this->html) {
            $this->mailEmail->html($this->html);
        }

        if (! $this->html) {
            $this->mailEmail->html($this->message);
        }

        try {
            $this->mailMailer->send($this->mailEmail);
        } catch (\Throwable $th) {
            $this->logError($th->getMessage(), $this->toArray());

            return false;
        }

        $this->logSent();

        return true;
    }

    public function toArray(): array
    {
        return [
            'mailer' => $this->mailer,
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption,
            'username' => $this->username,
            'password' => $this->password,
            'to' => $this->to,
            'from' => $this->from,
            'replyTo' => $this->replyTo,
            'subject' => $this->subject,
            'message' => $this->message,
            'html' => $this->html,
        ];
    }
}
