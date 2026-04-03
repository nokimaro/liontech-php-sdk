<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Request;

use JsonSerializable;

final readonly class RefreshTokenRequest implements JsonSerializable
{
    public function __construct(
        public string $refreshToken,
    ) {
    }

    /**
     * @return array{refreshToken: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'refreshToken' => $this->refreshToken,
        ];
    }
}
