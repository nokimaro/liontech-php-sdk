<?php

declare(strict_types=1);

use LionTech\SDK\Enums\OrderStatus;

it('has correct order statuses', function (): void {
    expect(OrderStatus::cases())->toHaveCount(6);
    expect(OrderStatus::CREATED->value)->toBe('CREATED');
    expect(OrderStatus::CANCELLED->value)->toBe('CANCELLED');
    expect(OrderStatus::PARTIALLY_PAID->value)->toBe('PARTIALLY_PAID');
    expect(OrderStatus::PAID->value)->toBe('PAID');
    expect(OrderStatus::PARTIALLY_REFUNDED->value)->toBe('PARTIALLY_REFUNDED');
    expect(OrderStatus::REFUNDED->value)->toBe('REFUNDED');
});

it('identifies final states correctly', function (): void {
    expect(OrderStatus::PAID->isFinal())->toBeTrue();
    expect(OrderStatus::CANCELLED->isFinal())->toBeTrue();
    expect(OrderStatus::REFUNDED->isFinal())->toBeTrue();
    expect(OrderStatus::CREATED->isFinal())->toBeFalse();
    expect(OrderStatus::PARTIALLY_PAID->isFinal())->toBeFalse();
    expect(OrderStatus::PARTIALLY_REFUNDED->isFinal())->toBeFalse();
});

it('identifies refundable states correctly', function (): void {
    expect(OrderStatus::PAID->isRefundable())->toBeTrue();
    expect(OrderStatus::PARTIALLY_PAID->isRefundable())->toBeTrue();
    expect(OrderStatus::PARTIALLY_REFUNDED->isRefundable())->toBeTrue();
    expect(OrderStatus::CREATED->isRefundable())->toBeFalse();
    expect(OrderStatus::CANCELLED->isRefundable())->toBeFalse();
    expect(OrderStatus::REFUNDED->isRefundable())->toBeFalse();
});

it('identifies cancelable states correctly', function (): void {
    expect(OrderStatus::CREATED->isCancelable())->toBeTrue();
    expect(OrderStatus::PAID->isCancelable())->toBeFalse();
    expect(OrderStatus::CANCELLED->isCancelable())->toBeFalse();
});
