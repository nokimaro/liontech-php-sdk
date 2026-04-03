<?php

declare(strict_types=1);

namespace LionTech\SDK\ValueObjects;

use JsonSerializable;

final readonly class CallbackUrl implements JsonSerializable
{
    public function __construct(
        public string $url,
    ) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid URL: {$url}");
        }
    }

    public function jsonSerialize(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
