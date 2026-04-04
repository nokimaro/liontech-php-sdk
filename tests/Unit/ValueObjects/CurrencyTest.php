<?php

declare(strict_types=1);

use Nokimaro\LionTech\ValueObjects\Currency;

it('has correct symbol for USD', function (): void {
    expect(Currency::USD->symbol())->toBe('$');
});

it('has correct symbol for EUR', function (): void {
    expect(Currency::EUR->symbol())->toBe('€');
});

it('has correct symbol for RUB', function (): void {
    expect(Currency::RUB->symbol())->toBe('₽');
});

it('has correct symbol for GBP', function (): void {
    expect(Currency::GBP->symbol())->toBe('£');
});

it('has correct symbol for KZT', function (): void {
    expect(Currency::KZT->symbol())->toBe('₸');
});

it('has correct symbol for CNY', function (): void {
    expect(Currency::CNY->symbol())->toBe('¥');
});

it('has correct symbol for TRY', function (): void {
    expect(Currency::TRY->symbol())->toBe('₺');
});

it('has correct symbol for JPY', function (): void {
    expect(Currency::JPY->symbol())->toBe('¥');
});

it('is always fiat', function (): void {
    foreach (Currency::cases() as $currency) {
        expect($currency->isFiat())
            ->toBeTrue();
    }
});
