<?php

namespace Kiwilan\Notifier\Notifier\Discord;

use Carbon\Carbon;
use DateTime;

class NotifierDiscordRich extends NotifierDiscordContainer
{
    protected function __construct(
        protected ?string $description = null,
        protected ?string $username = null,
        protected ?string $avatarUrl = null,
        protected ?string $authorName = null,
        protected ?string $authorUrl = null,
        protected ?string $authorIconUrl = null,
        protected ?string $url = null,
        protected ?string $title = null,
        protected ?string $timestamp = null,
        protected ?string $color = null,
        protected ?array $fields = null,
        protected ?string $thumbnail = null,
        protected ?string $image = null,
        protected ?string $footerText = null,
        protected ?string $footerIconUrl = null,
    ) {
    }

    public static function create(string $webhook, string $description): self
    {
        $self = new self($description);
        $self->webhook = $webhook;

        return $self;
    }

    public function user(string $username, ?string $avatarUrl = null): self
    {
        $this->username = $username;

        if ($avatarUrl) {
            $this->avatarUrl = $avatarUrl;
        }

        return $this;
    }

    public function author(string $name, ?string $url = null, ?string $iconUrl = null): self
    {
        $this->authorName = $name;

        if ($url) {
            $this->authorUrl = $url;
        }

        if ($iconUrl) {
            $this->authorIconUrl = $iconUrl;
        }

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function title($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function timestamp(Carbon|DateTime $timestamp): self
    {
        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->toDateTime();
        }

        $this->timestamp = $timestamp->format(DateTime::ATOM);

        return $this;
    }

    public function footer(string $footer, ?string $iconUrl = null): self
    {
        $this->footerText = $footer;

        if ($iconUrl) {
            $this->footerIconUrl = $iconUrl;
        }

        return $this;
    }

    /**
     * Set a color to rich embed, you can use shortcut methods like `success`, `warning`, `error`
     *
     * @param  string  $color  Add hex color code (with or without `#` prefix)
     */
    public function color(string $color): self
    {
        if (str_contains($color, '#')) {
            $color = str_replace('#', '', $color);
        }

        $this->color = $color;

        return $this;
    }

    /**
     * Set a green color to rich embed
     */
    public function colorSuccess(): self
    {
        $this->color = $this->getShortcutColor('success');

        return $this;
    }

    /**
     * Set a yellow color to rich embed
     */
    public function colorWarning(): self
    {
        $this->color = $this->getShortcutColor('warning');

        return $this;
    }

    /**
     * Set a red color to rich embed
     */
    public function colorError(): self
    {
        $this->color = $this->getShortcutColor('error');

        return $this;
    }

    private function getShortcutColor(string $color): string
    {
        return match ($color) {
            'success' => '22c55e',
            'warning' => 'eab308',
            'error' => 'ef4444',
            default => '22c55e',
        };
    }

    /**
     * Add fields to rich embed
     *
     * @param  array{name: string, value: string|int}  $fields  Array of fields, each field should have `name` and `value`
     * @param  bool  $inline  Set to `true` if you want to display fields inline
     */
    public function fields(array $fields, bool $inline = false): self
    {
        foreach ($fields as $field) {
            $this->fields[] = [
                'name' => $field['name'] ?? 'Field',
                'value' => $field['value'] ?? 'Value',
                'inline' => $inline,
            ];
        }

        return $this;
    }

    public function thumbnail(string $url): self
    {
        $this->thumbnail = $url;

        return $this;
    }

    public function image(string $url): self
    {
        $this->image = $url;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'avatar_url' => $this->avatarUrl,
            'embeds' => [
                [
                    'author' => [
                        'name' => $this->authorName,
                        'url' => $this->authorUrl,
                        'icon_url' => $this->authorIconUrl,
                    ],
                    'title' => $this->title,
                    'url' => $this->url,
                    'type' => 'rich',
                    'description' => $this->description,
                    'fields' => $this->fields,
                    'color' => hexdec($this->color),
                    'thumbnail' => [
                        'url' => $this->thumbnail,
                    ],
                    'image' => [
                        'url' => $this->image,
                    ],
                    'footer' => [
                        'text' => $this->footerText,
                        'icon_url' => $this->footerIconUrl,
                    ],
                    'timestamp' => $this->timestamp,
                ],
            ],
        ];
    }
}
