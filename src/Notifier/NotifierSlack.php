<?php

namespace Kiwilan\Notifier\Notifier;

use Kiwilan\Notifier\Notifier;
use Kiwilan\Notifier\Utils\NotifierRequest;

class NotifierSlack extends Notifier
{
    protected function __construct(
        protected string $webhook,
        protected ?string $message = null,
    ) {
    }

    public static function make(string $webhook): self
    {
        return new self($webhook);
    }

    public function message(array|string $message): self
    {
        $this->message = $this->arrayToString($message);

        return $this;
    }

    /**
     * Prepare message for sending.
     */
    private function createBody(): self
    {
        $this->requestData = [
            'text' => $this->message ?? '',
        ];

        return $this;
    }

    public function send(): bool
    {
        $this->createBody();
        // $spatie->message($this->message);
        // $spatie->blocks([
        //     [
        //         'type' => 'section',
        //         'text' => [
        //             'type' => 'mrkdwn',
        //             'text' => $this->message,
        //         ],
        //     ],
        // ]);
        // // $payload = $this->text
        // //     ? ['type' => 'mrkdwn', 'text' => $this->text]
        // //     : ['blocks' => $this->blocks];

        // // if ($this->channel) {
        // //     $payload['channel'] = $this->channel;
        // // }

        // // Http::post($this->webhookUrl, $payload)->throw();
        // $this->request = NotifierRequest::make($this->webhook)
        //     // ->requestData([
        //     //     'type' => 'mrkdwn',
        //     //     'text' => 'Hello, Slack!',
        //     // ])
        //     ->requestData([
        //         'blocks' => [
        //             [
        //                 'type' => 'section',
        //                 'text' => [
        //                     'type' => 'mrkdwn',
        //                     'text' => 'Danny Torrence left the following review for your property:',
        //                 ],
        //             ],
        //             [
        //                 'type' => 'section',
        //                 'block_id' => 'section567',
        //                 'text' => [
        //                     'type' => 'mrkdwn',
        //                     'text' => "<https://example.com|Overlook Hotel> \n :star: \n Doors had too many axe holes, guest in room 237 was far too rowdy, whole place felt stuck in the 1920s.",
        //                 ],
        //                 'accessory' => [
        //                     'type' => 'image',
        //                     'image_url' => 'https://is5-ssl.mzstatic.com/image/thumb/Purple3/v4/d3/72/5c/d3725c8f-c642-5d69-1904-aa36e4297885/source/256x256bb.jpg',
        //                     'alt_text' => 'Haunted hotel image',
        //                 ],
        //             ],
        //         ],
        //     ])
        //     ->send();

        if ($this->request->getStatusCode() !== 200) {
            $this->logError("status code {$this->request->getStatusCode()}", [
                $this->request->toArray(),
            ]);

            return false;
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'webhook' => $this->webhook,
            'message' => $this->message,
            'request' => $this->request->toArray(),
        ];
    }
}
