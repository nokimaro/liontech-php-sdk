<?php

declare(strict_types=1);

use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

it('creates money value object', function (): void {
    $money = new Money('100.00', Currency::USD);

    expect($money->amount)
        ->toBe('100.00');
    expect($money->currency)
        ->toBe(Currency::USD);
});

it('creates money from string', function (): void {
    $money = Money::fromString('50.50', 'EUR');

    expect($money->amount)
        ->toBe('50.50');
    expect($money->currency)
        ->toBe(Currency::EUR);
});

it('creates money from array', function (): void {
    $money = Money::fromArray([
        'value' => '200.00',
        'currency' => 'RUB',
    ]);

    expect($money->amount)
        ->toBe('200.00');
    expect($money->currency)
        ->toBe(Currency::RUB);
});

it('serializes to JSON', function (): void {
    $money = new Money('150.00', Currency::USD);
    $json = $money->jsonSerialize();

    expect($json)
        ->toBe([
            'value' => '150.00',
            'currency' => 'USD',
        ]);
});

it('is immutable', function (): void {
    $money = new Money('100.00', Currency::USD);
    $reflection = new ReflectionClass($money);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
