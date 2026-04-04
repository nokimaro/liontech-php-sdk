<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Requests;

use JsonSerializable;

final readonly class SbpData implements JsonSerializable
{
    public function __construct(
        public string $bank,
    ) {
    }

    /**
     * @return array{bank: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'bank' => $this->bank,
        ];
    }
}
