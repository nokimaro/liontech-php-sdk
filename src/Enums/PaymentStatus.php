<?php

declare(strict_types=1);

namespace LionTech\SDK\Enums;

enum PaymentStatus: string
{
    case OPERATION = 'OPERATION';
    case NEW = 'NEW';
    case PENDING = 'PENDING';
    case DECLINED = 'DECLINED';
    case AUTHORIZED = 'AUTHORIZED';
    case RECONCILED = 'RECONCILED';

    public function isFinal(): bool
    {
        return match ($this) {
            self::DECLINED, self::RECONCILED => true,
            default => false,
        };
    }

    public function isSuccessful(): bool
    {
        return $this === self::RECONCILED;
    }

    public function isDeclined(): bool
    {
        return $this === self::DECLINED;
    }

    public function isAuthorized(): bool
    {
        return $this === self::AUTHORIZED;
    }

    public function isPending(): bool
    {
        return match ($this) {
            self::OPERATION, self::NEW, self::PENDING, self::AUTHORIZED => true,
            default => false,
        };
    }
}
