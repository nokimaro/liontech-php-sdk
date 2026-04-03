<?php

declare(strict_types=1);

namespace LionTech\SDK\Enums;

enum OrderStatus: string
{
    case CREATED = 'CREATED';
    case CANCELLED = 'CANCELLED';
    case PARTIALLY_PAID = 'PARTIALLY_PAID';
    case PAID = 'PAID';
    case PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
    case REFUNDED = 'REFUNDED';

    public function isFinal(): bool
    {
        return match ($this) {
            self::PAID, self::CANCELLED, self::REFUNDED => true,
            default => false,
        };
    }

    public function isRefundable(): bool
    {
        return match ($this) {
            self::PAID, self::PARTIALLY_PAID, self::PARTIALLY_REFUNDED => true,
            default => false,
        };
    }

    public function isCancelable(): bool
    {
        return $this === self::CREATED;
    }
}
