<?php

declare(strict_types=1);

namespace LionTech\SDK\ValueObjects;

use JsonSerializable;

final readonly class EncryptedCardData implements JsonSerializable
{
    public function __construct(
        public string $encryptedCardData,
        public ?string $cardHolder = null,
    ) {
    }

    /**
     * @return array{encryptedCardData: string, cardHolder?: string}
     */
    public function jsonSerialize(): array
    {
        $data = [
            'encryptedCardData' => $this->encryptedCardData,
        ];

        if ($this->cardHolder !== null) {
            $data['cardHolder'] = $this->cardHolder;
        }

        return $data;
    }
}
