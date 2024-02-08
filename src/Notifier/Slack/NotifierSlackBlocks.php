<?php

namespace Kiwilan\Notifier\Notifier\Slack;

use Kiwilan\Notifier\Utils\NotifierHelpers;

class NotifierSlackBlocks extends NotifierSlackContainer
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
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => 'Danny Torrence left the following review for your property:',
                    ],
                    'fields' => [
                        [
                            'type' => 'mrkdwn',
                            'text' => '*Markdown!*',
                        ],
                        [
                            'type' => 'plain_text',
                            'text' => str_repeat('a', 1997).'...',
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => 'Location: 123 Main Street, New York, NY 10010',
                        ],
                    ],
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'actions',
                    'elements' => [
                        [
                            'type' => 'button',
                            'text' => [
                                'type' => 'plain_text',
                                'text' => 'Example Button',
                            ],
                            'action_id' => 'button_example-button',
                        ],
                        [
                            'type' => 'button',
                            'text' => [
                                'type' => 'plain_text',
                                'text' => 'Scary Button',
                            ],
                            'action_id' => 'button_scary-button',
                            'style' => 'danger',
                        ],
                    ],
                ],
                [
                    'type' => 'header',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'Budget Performance',
                    ],
                ],
                [
                    'type' => 'image',
                    'image_url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                    'alt_text' => 'notifier banner',
                ],
                [
                    'type' => 'section',
                    'block_id' => 'section567',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => 'Markdown *can* be so fun!',
                    ],
                    'accessory' => [
                        'type' => 'image',
                        'image_url' => 'https://raw.githubusercontent.com/kiwilan/notifier-laravel/main/docs/banner.jpg',
                        'alt_text' => 'notifier banner',
                    ],
                ],
            ],
        ];
    }
}
