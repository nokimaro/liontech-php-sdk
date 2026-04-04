<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Response\MerchantAccount;
use LionTech\SDK\ValueObjects\Currency;

it('creates merchant account from array', function (): void {
    $data = [
        'accountId' => 'acc_123',
        'accountTypeId' => 'type_1',
        'mstId' => 'mst_1',
        'currency' => 'USD',
        'balance' => '1000.00',
        'createdAt' => '2024-01-01T00:00:00Z',
        'updatedAt' => '2024-01-02T00:00:00Z',
        'validOn' => '2024-01-01T00:00:00Z',
    ];

    $account = MerchantAccount::fromArray($data);

    expect($account->accountId)->toBe('acc_123');
    expect($account->accountTypeId)->toBe('type_1');
    expect($account->mstId)->toBe('mst_1');
    expect($account->currency)->toBe(Currency::USD);
    expect($account->balance)->toBe('1000.00');
    expect($account->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    expect($account->updatedAt)->toBeInstanceOf(DateTimeImmutable::class);
    expect($account->validOn)->toBeInstanceOf(DateTimeImmutable::class);
});

it('creates merchant account with different currency', function (): void {
    $data = [
        'accountId' => 'acc_456',
        'accountTypeId' => 'type_2',
        'mstId' => 'mst_2',
        'currency' => 'EUR',
        'balance' => '500.00',
        'createdAt' => '2024-01-01T00:00:00Z',
        'updatedAt' => '2024-01-02T00:00:00Z',
        'validOn' => '2024-01-01T00:00:00Z',
    ];

    $account = MerchantAccount::fromArray($data);

    expect($account->currency)->toBe(Currency::EUR);
    expect($account->balance)->toBe('500.00');
});

it('is immutable', function (): void {
    $data = [
        'accountId' => 'acc_123',
        'accountTypeId' => 'type_1',
        'mstId' => 'mst_1',
        'currency' => 'USD',
        'balance' => '1000.00',
        'createdAt' => '2024-01-01T00:00:00Z',
        'updatedAt' => '2024-01-02T00:00:00Z',
        'validOn' => '2024-01-01T00:00:00Z',
    ];

    $account = MerchantAccount::fromArray($data);
    $reflection = new ReflectionClass($account);

    expect($reflection->isReadOnly())->toBeTrue();
});
