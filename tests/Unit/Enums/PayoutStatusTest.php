<?php

declare(strict_types=1);

use LionTech\SDK\Enums\PayoutStatus;

it('has correct values', function (): void {
    expect(PayoutStatus::PENDING->value)->toBe('PENDING');
    expect(PayoutStatus::SUCCEEDED->value)->toBe('SUCCEEDED');
    expect(PayoutStatus::DECLINED->value)->toBe('DECLINED');
    expect(PayoutStatus::FAILED->value)->toBe('FAILED');
});

it('identifies final states', function (): void {
    expect(PayoutStatus::SUCCEEDED->isFinal())->toBeTrue();
    expect(PayoutStatus::DECLINED->isFinal())->toBeTrue();
    expect(PayoutStatus::FAILED->isFinal())->toBeTrue();
});

it('identifies non-final states', function (): void {
    expect(PayoutStatus::PENDING->isFinal())->toBeFalse();
});

it('identifies successful state', function (): void {
    expect(PayoutStatus::SUCCEEDED->isSuccessful())->toBeTrue();
    expect(PayoutStatus::PENDING->isSuccessful())->toBeFalse();
    expect(PayoutStatus::DECLINED->isSuccessful())->toBeFalse();
    expect(PayoutStatus::FAILED->isSuccessful())->toBeFalse();
});
