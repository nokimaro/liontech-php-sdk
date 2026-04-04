<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Enums;

enum RefundStatus: string
{
    case PENDING = 'PENDING';
    case SUCCEEDED = 'SUCCEEDED';
    case DECLINED = 'DECLINED';
    case FAILED = 'FAILED';

    public function isFinal(): bool
    {
        return match ($this) {
            self::SUCCEEDED, self::DECLINED, self::FAILED => true,
            default => false,
        };
    }

    public function isSuccessful(): bool
    {
        return $this === self::SUCCEEDED;
    }
}
