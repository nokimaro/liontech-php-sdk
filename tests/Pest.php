<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\CreateOrderRequest;
use LionTech\SDK\DTOs\Request\CreatePaymentRequest;
use LionTech\SDK\DTOs\Request\CreatePayoutRequest;
use LionTech\SDK\DTOs\Request\CreateRefundRequest;
use LionTech\SDK\DTOs\Request\CustomerData;
use LionTech\SDK\DTOs\Request\RefreshTokenRequest;
use LionTech\SDK\DTOs\Response\MerchantAccount;
use LionTech\SDK\DTOs\Response\MerchantTokensRefreshResponse;
use LionTech\SDK\DTOs\Response\OrderResponse;
use LionTech\SDK\DTOs\Response\PaymentResponse;
use LionTech\SDK\DTOs\Response\PayoutResponse;
use LionTech\SDK\DTOs\Response\RefundResponse;
use LionTech\SDK\DTOs\Response\SavedPaymentMethod;
use LionTech\SDK\Enums\OrderStatus;
use LionTech\SDK\Enums\PaymentStatus;
use LionTech\SDK\Enums\PayoutStatus;
use LionTech\SDK\Enums\RefundStatus;
use LionTech\SDK\Exceptions\ApiErrorResponse;
use LionTech\SDK\Exceptions\Auth\AuthenticationException;
use LionTech\SDK\Exceptions\Auth\TokenExpiredException;
use LionTech\SDK\Exceptions\Business\ConflictException;
use LionTech\SDK\Exceptions\RateLimitException;
use LionTech\SDK\Exceptions\ResourceNotFoundException;
use LionTech\SDK\Exceptions\SdkException;
use LionTech\SDK\Exceptions\Transport\TransportException;
use LionTech\SDK\Exceptions\Validation\ValidationException;
use LionTech\SDK\Helpers\CardEncryptor;
use LionTech\SDK\Helpers\WebhookSignatureVerifier;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\LionTechSDK;
use LionTech\SDK\ValueObjects\CallbackUrl;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\EncryptedCardData;
use LionTech\SDK\ValueObjects\Money;
use LionTech\SDK\ValueObjects\PaymentData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/*
|--------------------------------------------------------------------------
| Test Case Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeValidMoney', function (): void {
    expect($this->value)->toBeInstanceOf(Money::class);
    expect($this->value->amount)->toBeString();
    expect($this->value->currency)->toBeInstanceOf(Currency::class);
});

expect()->extend('toBeValidOrderResponse', function (): void {
    expect($this->value)->toBeInstanceOf(OrderResponse::class);
    expect($this->value->orderId)->toBeString();
    expect($this->value->status)->toBeInstanceOf(OrderStatus::class);
});

expect()->extend('toBeValidPaymentResponse', function (): void {
    expect($this->value)->toBeInstanceOf(PaymentResponse::class);
    expect($this->value->paymentId)->toBeString();
    expect($this->value->status)->toBeInstanceOf(PaymentStatus::class);
});
