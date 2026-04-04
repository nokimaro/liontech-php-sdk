<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Response\OrderResponse;
use LionTech\SDK\DTOs\Response\PaymentResponse;
use LionTech\SDK\Enums\OrderStatus;
use LionTech\SDK\Enums\PaymentStatus;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/*
|--------------------------------------------------------------------------
| Test Case Expectations
|--------------------------------------------------------------------------
*/

expect()
    ->extend('toBeValidMoney', function (): void {
        expect($this->value)->toBeInstanceOf(Money::class);
        expect($this->value->amount)
            ->toBeString();
        expect($this->value->currency)
            ->toBeInstanceOf(Currency::class);
    });

expect()
    ->extend('toBeValidOrderResponse', function (): void {
        expect($this->value)->toBeInstanceOf(OrderResponse::class);
        expect($this->value->orderId)
            ->toBeString();
        expect($this->value->status)
            ->toBeInstanceOf(OrderStatus::class);
    });

expect()
    ->extend('toBeValidPaymentResponse', function (): void {
        expect($this->value)->toBeInstanceOf(PaymentResponse::class);
        expect($this->value->paymentId)
            ->toBeString();
        expect($this->value->status)
            ->toBeInstanceOf(PaymentStatus::class);
    });

/*
|--------------------------------------------------------------------------
| Mockery Setup
|--------------------------------------------------------------------------
*/

beforeEach(function (): void {
    // Ensure Mockery is available
});

afterEach(function (): void {
    Mockery::close();
});

/*
|--------------------------------------------------------------------------
| Shared Test Helpers
|--------------------------------------------------------------------------
*/

function mockResponse(array $data): ResponseInterface
{
    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->andReturn(json_encode($data));

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getBody')
        ->andReturn($stream);

    return $response;
}
