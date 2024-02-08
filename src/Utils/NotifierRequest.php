<?php

namespace Kiwilan\Notifier\Utils;

class NotifierRequest
{
    protected function __construct(
        protected string $url,
        protected string $mode = 'stream',
        protected bool $modeAuto = true,
        protected array $headers = [],
        protected array $request_data = [],
        protected ?array $response_body = [],
        protected ?int $status_code = null,
        protected string $method = 'POST',
        protected bool $json = true,
    ) {
    }

    /**
     * Create a new NotifierRequest instance.
     */
    public static function make(string $url)
    {
        $url = trim($url);

        return new self($url);
    }

    /**
     * Use stream to send HTTP request.
     */
    public function useStream(): self
    {
        $this->mode = 'stream';
        $this->modeAuto = false;

        return $this;
    }

    /**
     * Use cURL to send HTTP request.
     */
    public function useCurl(): self
    {
        $this->mode = 'curl';
        $this->modeAuto = false;

        return $this;
    }

    /**
     * Use Guzzle to send HTTP request.
     */
    public function useGuzzle(): self
    {
        $this->mode = 'guzzle';
        $this->modeAuto = false;

        return $this;
    }

    /**
     * Set the request data.
     */
    public function requestData(array $data): self
    {
        $this->request_data = $data;

        return $this;
    }

    /**
     * Set the request method: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`.
     */
    public function method(string $method): self
    {
        $this->method = strtoupper($method);

        return $this;
    }

    public function headers(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function asJson(): self
    {
        $this->json = true;

        return $this;
    }

    public function asForm(): self
    {
        $this->json = false;

        return $this;
    }

    /**
     * Send HTTP request.
     */
    public function send(): self
    {
        try {
            if ($this->modeAuto) {
                $this->mode = config('notifier.client');
            }

            if ($this->mode === 'stream') {
                $this->stream();
            } elseif ($this->mode === 'curl') {
                $this->curl();
            } elseif ($this->mode === 'guzzle') {
                $this->guzzle();
            }
        } catch (\Throwable $th) {
            $this->status_code = 500;
            $this->response_body = [
                'error' => $th->getMessage(),
            ];
        }

        return $this;
    }

    /**
     * Send HTTP request using stream.
     */
    private function stream(): void
    {
        $headers = $this->headers;
        if ($this->json) {
            $headers[] = 'Content-Type: application/json';
        } else {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }

        $context = stream_context_create([
            'http' => [
                'method' => $this->method,
                'header' => implode("\r\n", $headers),
                'content' => $this->json ? json_encode($this->request_data) : http_build_query($this->request_data),
            ],
        ]);
        $response = file_get_contents($this->url, false, $context);
        $headers = $http_response_header;

        $this->status_code = (int) explode(' ', $headers[0])[1];
        $this->response_body = json_decode($response, true);
    }

    /**
     * Send HTTP request using cURL.
     */
    private function curl(): void
    {
        $ch = curl_init($this->url);
        $headers = $this->headers;
        if ($this->json) {
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($this->method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } elseif ($this->method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        } elseif ($this->method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        } elseif ($this->method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->request_data));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->response_body = json_decode($response, true);

        curl_close($ch);
    }

    /**
     * Send HTTP request using Guzzle.
     */
    private function guzzle(): void
    {
        if (! \Composer\InstalledVersions::isInstalled('guzzlehttp/guzzle')) {
            throw new \Exception('Package `guzzlehttp/guzzle` not installed, see: https://github.com/guzzle/guzzle');
        }

        $client = new \GuzzleHttp\Client();
        $body = $this->json ? 'json' : 'form_params';
        $response = $client->request($this->method, $this->url, [
            $body => $this->request_data,
        ]);

        $this->status_code = $response->getStatusCode();
        $this->response_body = json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get the request mode: `stream`, `curl`, or `guzzle`.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get the request URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the request data.
     */
    public function getRequestData(): array
    {
        return $this->request_data;
    }

    /**
     * Get the response body.
     */
    public function getResponseBody(): array
    {
        return $this->response_body;
    }

    /**
     * Get the status code.
     */
    public function getStatusCode(): ?int
    {
        return $this->status_code;
    }

    public function toArray(): array
    {
        return [
            'mode' => $this->mode,
            'url' => $this->url,
            'request_data' => $this->request_data,
            'response_body' => $this->response_body,
            'status_code' => $this->status_code,
        ];
    }
}
