<?php

declare(strict_types=1);

namespace LionTech\SDK\Http;

final class TokenStore
{
    public function __construct(
        private string $accessToken,
        private ?string $refreshToken = null
    ) {
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }

    public function refreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function update(string $accessToken, string $refreshToken): void
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }
}
