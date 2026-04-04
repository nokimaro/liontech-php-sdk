<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Responses;

use Nokimaro\LionTech\Json;

final readonly class MerchantTokensRefreshResponse
{
    public function __construct(
        public string $accessToken,
        public \DateTimeImmutable $accessTokenExpireAt,
        public string $refreshToken,
        public \DateTimeImmutable $refreshTokenExpireAt,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: Json::getString($data, 'accessToken'),
            accessTokenExpireAt: new \DateTimeImmutable(Json::getString($data, 'accessTokenExpireAt')),
            refreshToken: Json::getString($data, 'refreshToken'),
            refreshTokenExpireAt: new \DateTimeImmutable(Json::getString($data, 'refreshTokenExpireAt')),
        );
    }
}
