<?php

declare(strict_types=1);

namespace LionTech\SDK\ValueObjects;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case RUB = 'RUB';
    case GBP = 'GBP';
    case KZT = 'KZT';
    case CNY = 'CNY';
    case TRY = 'TRY';
    case JPY = 'JPY';

    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::RUB => '₽',
            self::GBP => '£',
            self::KZT => '₸',
            self::CNY => '¥',
            self::TRY => '₺',
            self::JPY => '¥',
        };
    }

    public function isFiat(): bool
    {
        return true;
    }
}
