<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Response;

final readonly class MerchantTokensRefreshResponse
{
    public function __construct(
        public string $accessToken,
        public \DateTimeImmutable $accessTokenExpireAt,
        public string $refreshToken,
        public \DateTimeImmutable $refreshTokenExpireAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: $data['accessToken'],
            accessTokenExpireAt: new \DateTimeImmutable($data['accessTokenExpireAt']),
            refreshToken: $data['refreshToken'],
            refreshTokenExpireAt: new \DateTimeImmutable($data['refreshTokenExpireAt']),
        );
    }
}
