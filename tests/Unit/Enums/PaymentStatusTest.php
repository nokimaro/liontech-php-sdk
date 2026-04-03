<?php

declare(strict_types=1);

use LionTech\SDK\Enums\PaymentStatus;

it('has correct payment statuses', function (): void {
    expect(PaymentStatus::cases())->toHaveCount(6);
    expect(PaymentStatus::OPERATION->value)->toBe('OPERATION');
    expect(PaymentStatus::NEW->value)->toBe('NEW');
    expect(PaymentStatus::PENDING->value)->toBe('PENDING');
    expect(PaymentStatus::DECLINED->value)->toBe('DECLINED');
    expect(PaymentStatus::AUTHORIZED->value)->toBe('AUTHORIZED');
    expect(PaymentStatus::RECONCILED->value)->toBe('RECONCILED');
});

it('identifies final states correctly', function (): void {
    expect(PaymentStatus::DECLINED->isFinal())->toBeTrue();
    expect(PaymentStatus::RECONCILED->isFinal())->toBeTrue();
    expect(PaymentStatus::OPERATION->isFinal())->toBeFalse();
    expect(PaymentStatus::NEW->isFinal())->toBeFalse();
    expect(PaymentStatus::PENDING->isFinal())->toBeFalse();
    expect(PaymentStatus::AUTHORIZED->isFinal())->toBeFalse();
});

it('identifies successful state', function (): void {
    expect(PaymentStatus::RECONCILED->isSuccessful())->toBeTrue();
    expect(PaymentStatus::DECLINED->isSuccessful())->toBeFalse();
});

it('identifies declined state', function (): void {
    expect(PaymentStatus::DECLINED->isDeclined())->toBeTrue();
    expect(PaymentStatus::RECONCILED->isDeclined())->toBeFalse();
});

it('identifies authorized state', function (): void {
    expect(PaymentStatus::AUTHORIZED->isAuthorized())->toBeTrue();
    expect(PaymentStatus::RECONCILED->isAuthorized())->toBeFalse();
});

it('identifies pending states', function (): void {
    expect(PaymentStatus::OPERATION->isPending())->toBeTrue();
    expect(PaymentStatus::NEW->isPending())->toBeTrue();
    expect(PaymentStatus::PENDING->isPending())->toBeTrue();
    expect(PaymentStatus::AUTHORIZED->isPending())->toBeTrue();
    expect(PaymentStatus::DECLINED->isPending())->toBeFalse();
    expect(PaymentStatus::RECONCILED->isPending())->toBeFalse();
});
