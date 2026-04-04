<?php

declare(strict_types=1);

namespace Nokimaro\LionTech;

use Nokimaro\LionTech\Http\Transport;

final class ClientBuilder
{
    private ?string $accessToken = null;

    private ?string $refreshToken = null;

    private ?Transport $httpClient = null;

    private string $baseUrl = 'https://api.liontechnology.ai';

    private string $secureUrl = 'https://secure.liontechnology.ai';

    public function accessToken(string $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    public function refreshToken(string $token): self
    {
        $this->refreshToken = $token;

        return $this;
    }

    public function httpClient(Transport $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    public function baseUrl(string $url): self
    {
        $this->baseUrl = rtrim($url, '/');

        return $this;
    }

    public function secureUrl(string $url): self
    {
        $this->secureUrl = rtrim($url, '/');

        return $this;
    }

    public function sandbox(): self
    {
        $this->baseUrl = 'https://api.sandbox.liontechnology.ai';
        $this->secureUrl = 'https://secure.sandbox.liontechnology.ai';

        return $this;
    }

    public function build(): Client
    {
        if ($this->accessToken === null) {
            throw new \InvalidArgumentException('Access token is required. Call accessToken() first.');
        }

        return new Client(
            accessToken: $this->accessToken,
            refreshToken: $this->refreshToken,
            httpClient: $this->httpClient,
            baseUrl: $this->baseUrl,
            secureUrl: $this->secureUrl,
        );
    }
}
