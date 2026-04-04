<?php

declare(strict_types=1);

use LionTech\SDK\Enums\RefundStatus;

it('has correct values', function (): void {
    expect(RefundStatus::PENDING->value)->toBe('PENDING');
    expect(RefundStatus::SUCCEEDED->value)->toBe('SUCCEEDED');
    expect(RefundStatus::DECLINED->value)->toBe('DECLINED');
    expect(RefundStatus::FAILED->value)->toBe('FAILED');
});

it('identifies final states', function (): void {
    expect(RefundStatus::SUCCEEDED->isFinal())->toBeTrue();
    expect(RefundStatus::DECLINED->isFinal())->toBeTrue();
    expect(RefundStatus::FAILED->isFinal())->toBeTrue();
});

it('identifies non-final states', function (): void {
    expect(RefundStatus::PENDING->isFinal())->toBeFalse();
});

it('identifies successful state', function (): void {
    expect(RefundStatus::SUCCEEDED->isSuccessful())->toBeTrue();
    expect(RefundStatus::PENDING->isSuccessful())->toBeFalse();
    expect(RefundStatus::DECLINED->isSuccessful())->toBeFalse();
    expect(RefundStatus::FAILED->isSuccessful())->toBeFalse();
});
