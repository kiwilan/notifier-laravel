<?php

namespace Kiwilan\Notifier\Notifier\Slack;

use Kiwilan\Notifier\Utils\NotifierHelpers;

class NotifierSlackAttachment extends NotifierSlackContainer
{
    protected function __construct(
        protected ?string $text = null,
    ) {
    }

    public static function create(string $webhook, string $message): self
    {
        $message = NotifierHelpers::truncate($message);

        $self = new self($message);
        $self->webhook = $webhook;

        return $self;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'attachments' => [
                [
                    'mrkdwn_in' => ['text'],
                    'color' => '#36a64f',
                    'pretext' => 'Optional pre-text that appears above the attachment block',
                    'author_name' => 'author_name',
                    'author_link' => 'http://flickr.com/bobby/',
                    'author_icon' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                    'title' => 'title',
                    'title_link' => 'https://api.slack.com/',
                    'text' => 'Optional `text` that appears within the attachment',
                    'fields' => [
                        [
                            'title' => "A field's title",
                            'value' => "This field's value",
                            'short' => false,
                        ],
                        [
                            'title' => "A short field's title",
                            'value' => "This field's value",
                            'short' => true,
                        ],
                        [
                            'title' => "A short field's title",
                            'value' => "This field's value",
                            'short' => true,
                        ],
                    ],
                    'image_url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                    'footer' => 'footer',
                    'footer_icon' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                    'ts' => 123456789,
                ],
            ],
        ];
    }
}
