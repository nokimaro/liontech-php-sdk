<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\SbpData;

it('creates sbp data', function (): void {
    $sbpData = new SbpData(bank: 'sberbank');

    expect($sbpData->bank)->toBe('sberbank');
});

it('serializes to JSON', function (): void {
    $sbpData = new SbpData(bank: 'sberbank');

    $json = $sbpData->jsonSerialize();

    expect($json)->toBe(['bank' => 'sberbank']);
});

it('is immutable', function (): void {
    $sbpData = new SbpData(bank: 'sberbank');
    $reflection = new ReflectionClass($sbpData);

    expect($reflection->isReadOnly())->toBeTrue();
});
