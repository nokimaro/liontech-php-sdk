<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\CustomerData;

it('creates customer data with minimal fields', function (): void {
    $customer = new CustomerData();

    expect($customer->accountId)
        ->toBeNull();
    expect($customer->email)
        ->toBeNull();
});

it('creates customer data with all fields', function (): void {
    $customer = new CustomerData(
        accountId: 'acc_123',
        email: 'test@example.com',
        fullName: 'John Doe',
        phone: '+1234567890',
        ip: '192.168.1.1',
        fingerprint: 'fp_123',
        address: '123 Main St',
        city: 'New York',
        state: 'NY',
        postalCode: '10001',
        country: 'US',
        neighborhood: 'Manhattan',
        birthdate: new DateTimeImmutable('1990-01-01'),
    );

    expect($customer->accountId)
        ->toBe('acc_123');
    expect($customer->email)
        ->toBe('test@example.com');
    expect($customer->fullName)
        ->toBe('John Doe');
    expect($customer->phone)
        ->toBe('+1234567890');
    expect($customer->ip)
        ->toBe('192.168.1.1');
    expect($customer->fingerprint)
        ->toBe('fp_123');
    expect($customer->address)
        ->toBe('123 Main St');
    expect($customer->city)
        ->toBe('New York');
    expect($customer->state)
        ->toBe('NY');
    expect($customer->postalCode)
        ->toBe('10001');
    expect($customer->country)
        ->toBe('US');
    expect($customer->neighborhood)
        ->toBe('Manhattan');
    expect($customer->birthdate)
        ->toBeInstanceOf(DateTimeImmutable::class);
});

it('serializes to JSON', function (): void {
    $customer = new CustomerData(email: 'test@example.com', fullName: 'John Doe', ip: '192.168.1.1');

    $json = $customer->jsonSerialize();

    expect($json)
        ->toHaveKeys(['email', 'fullName', 'ip']);
    expect($json['email'])->toBe('test@example.com');
    expect($json['fullName'])->toBe('John Doe');
    expect($json['ip'])->toBe('192.168.1.1');
});

it('does not include null fields in JSON', function (): void {
    $customer = new CustomerData(email: 'test@example.com');

    $json = $customer->jsonSerialize();

    expect($json)
        ->not->toHaveKey('accountId');
    expect($json)
        ->not->toHaveKey('phone');
    expect($json)
        ->toHaveKey('email');
});

it('is immutable', function (): void {
    $customer = new CustomerData(email: 'test@example.com');
    $reflection = new ReflectionClass($customer);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
