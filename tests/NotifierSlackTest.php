<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\Notifier\Facades\Notifier;
use Kiwilan\Notifier\Notifier as NotifierNotifier;
use Kiwilan\Notifier\Utils\NotifierRequest;

beforeEach(function () {
    Config::set('notifier.slack.webhook', dotenv()['NOTIFIER_SLACK_WEBHOOK']);
});

it('can use instance', function () {
    $facade = Notifier::slack();
    $instance = (new NotifierNotifier())->slack();

    expect($facade)->toEqual($instance);
    expect($facade)->toBeInstanceOf(NotifierNotifier::class);
});

it('can use', function () {
    $request = NotifierRequest::make(config('notifier.slack.webhook'))
        ->requestData([
            'text' => 'Content',
        ])
        ->useGuzzle()
        ->send();
    dump($request);

    $request = NotifierRequest::make(config('notifier.slack.webhook'))
        ->requestData([
            'username' => 'Ghostbot',
            'icon_emoji' => ':ghost:',
            'channel' => '#ghost-talk',
            'text' => [
                'type' => 'mrkdwn',
                'text' => '*Content with attachment*',
            ],
            'attachments' => [
                [
                    'title' => 'Laravel',
                    'title_link' => 'https://laravel.com',
                    'text' => 'Attachment Content',
                    'fallback' => 'Attachment Fallback',
                    'fields' => [
                        [
                            'title' => 'Project',
                            'value' => 'Laravel',
                            'short' => true,
                        ],
                    ],
                    'mrkdwn_in' => ['text'],
                    'footer' => 'Laravel',
                    'footer_icon' => 'https://laravel.com/fake.png',
                    'author_name' => 'Author',
                    'author_link' => 'https://laravel.com/fake_author',
                    'author_icon' => 'https://laravel.com/fake_author.png',
                    'ts' => 1234567890,
                ],
            ],
        ])
        ->send();
    dump($request);

    $request = NotifierRequest::make(config('notifier.slack.webhook'))
        ->requestData([
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => 'Go to slack!',
                    ],
                ],
            ],
        ])
        ->send();
    dump($request);

    $request = NotifierRequest::make(config('notifier.slack.webhook'))
        ->requestData([
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => 'Danny Torrence left the following review for your property:',
                    ],
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
        ])
        ->send();
    dump($request);
});

// it('can use command', function () {
//     $success = Artisan::call('notifier', [
//         'message' => 'Hello, Slack!',
//         '--type' => 'slack',
//         '--webhook' => config('notifier.slack.webhook'),
//     ]);

//     expect($success)->toBe(0);

//     $success = Artisan::call('notifier', [
//         'message' => 'Hello, Slack!',
//         '--type' => 'slack',
//     ]);

//     expect($success)->toBe(0);
// });
