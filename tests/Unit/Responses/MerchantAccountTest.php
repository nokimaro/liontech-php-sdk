<?php

declare(strict_types=1);

use Nokimaro\LionTech\Responses\MerchantAccount;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

it('creates merchant account from array with object balance', function (): void {
    $data = [
        'accountId' => 'acc_123',
        'accountTypeId' => 'type_1',
        'mstId' => 'mst_1',
        'currency' => 'USD',
        'balance' => [
            'value' => '1000.00',
            'currency' => 'USD',
        ],
        'createdAt' => '2024-01-01T00:00:00Z',
        'updatedAt' => '2024-01-02T00:00:00Z',
        'validOn' => '2024-01-01T00:00:00Z',
    ];

    $account = MerchantAccount::fromArray($data);

    expect($account->accountId)
        ->toBe('acc_123');
    expect($account->accountTypeId)
        ->toBe('type_1');
    expect($account->mstId)
        ->toBe('mst_1');
    expect($account->currency)
        ->toBe(Currency::USD);
    expect($account->balance)
        ->toBeInstanceOf(Money::class);
    expect($account->balance->amount)
        ->toBe('1000.00');
    expect($account->balance->currency)
        ->toBe(Currency::USD);
    expect($account->createdAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
    expect($account->updatedAt)
        ->toBeInstanceOf(DateTimeImmutable::class);
    expect($account->validOn)
        ->toBeInstanceOf(DateTimeImmutable::class);
});

it('falls back to zero balance when balance field is missing', function (): void {
    $data = [
        'accountId' => 'acc_456',
        'accountTypeId' => 'type_2',
        'mstId' => 'mst_2',
        'currency' => 'EUR',
        'createdAt' => '2024-01-01T00:00:00Z',
        'updatedAt' => '2024-01-02T00:00:00Z',
        'validOn' => '2024-01-01T00:00:00Z',
    ];

    $account = MerchantAccount::fromArray($data);

    expect($account->balance)
        ->toBeInstanceOf(Money::class);
    expect($account->balance->amount)
        ->toBe('0');
    expect($account->currency)
        ->toBe(Currency::EUR);
});

it('is immutable', function (): void {
    $data = [
        'accountId' => 'acc_123',
        'accountTypeId' => 'type_1',
        'mstId' => 'mst_1',
        'currency' => 'USD',
        'balance' => [
            'value' => '1000.00',
            'currency' => 'USD',
        ],
        'createdAt' => '2024-01-01T00:00:00Z',
        'updatedAt' => '2024-01-02T00:00:00Z',
        'validOn' => '2024-01-01T00:00:00Z',
    ];

    $account = MerchantAccount::fromArray($data);
    $reflection = new ReflectionClass($account);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
