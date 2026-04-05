# LionTech PHP SDK

[![Latest Version](https://img.shields.io/packagist/v/nokimaro/liontech-php-sdk?style=flat-square)](https://packagist.org/packages/nokimaro/liontech-php-sdk)
[![Tests](https://github.com/nokimaro/liontech-php-sdk/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/nokimaro/liontech-php-sdk/actions/workflows/ci.yml)
![Coverage](https://img.shields.io/badge/Coverage-96%25-2ECC71?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php)
[![License](https://img.shields.io/badge/license-MIT-blue?style=flat-square)](LICENSE.md)

Community-maintained PHP SDK for the [LionTech Payment Gateway](https://liontechnology.ai). This SDK provides a type-safe, domain-oriented interface for integrating LionTech's payment processing capabilities into your PHP applications.

> **Note:** This is an unofficial, community-maintained library. LionTech has no official PHP SDK.

## Features

- **Complete API Coverage**: Orders, Payments, Refunds, Payouts, Tokens, Transfers, Balances
- **Type-Safe**: Strong typing with PHP 8.3 enums, readonly classes, and typed properties
- **PSR Compliant**: PSR-4, PSR-7, PSR-17, PSR-18 compatible
- **Secure**: Webhook signature verification and RSA card encryption
- **Token Management**: Automatic token refresh support
- **Domain-Oriented**: Clean API with typed request/response objects instead of raw arrays
- **Extensible**: Bring your own PSR-18 HTTP client

## Requirements

- PHP 8.3 or higher
- PSR-18 HTTP Client implementation (Guzzle recommended)

## Installation

```bash
composer require nokimaro/liontech-php-sdk
```

If you don't have an HTTP client installed, we recommend Guzzle:

```bash
composer require guzzlehttp/guzzle
```

## Laravel Integration

If you're using Laravel, there's a dedicated package that wraps this SDK with Laravel-native features (service provider, facade, config file):

```bash
composer require nokimaro/liontech-laravel
```

See [nokimaro/liontech-laravel](https://github.com/nokimaro/liontech-laravel) for installation and usage instructions.

## Quick Start

### Basic Setup

```php
<?php

require_once 'vendor/autoload.php';

use Nokimaro\LionTech\Client;

$liontech = new Client(
    accessToken: 'your_access_token_here',
    // refreshToken: 'your_refresh_token',
    // baseUrl: 'https://api.sandbox.liontechnology.ai',
);
```

### Create an Order

```php
<?php

use Nokimaro\LionTech\Client;
use Nokimaro\LionTech\Requests\CreateOrderRequest;
use Nokimaro\LionTech\Requests\CustomerData;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

$orderRequest = new CreateOrderRequest(
    amount: new Money('100.00', Currency::USD),
    customer: new CustomerData(
        email: 'customer@example.com',
        fullName: 'John Doe',
        ip: '192.168.1.1',
    ),
    autoApprove: true,
    successUrl: 'https://your-site.com/success',
    declineUrl: 'https://your-site.com/decline',
    webhookUrl: 'https://your-site.com/webhook',
    description: 'Order #12345',
);

$order = $liontech->orders()->create($orderRequest);

echo "Order ID: {$order->orderId}\n";
echo "Pay URL: {$order->payUrl}\n";
```

### Process a Payment with Encrypted Card Data

```php
<?php

use Nokimaro\LionTech\Requests\CreatePaymentRequest;
use Nokimaro\LionTech\Requests\CustomerData;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\EncryptedCardData;
use Nokimaro\LionTech\ValueObjects\Money;
use Nokimaro\LionTech\ValueObjects\PaymentData;

// Encrypt the card data before sending
$encryptor = $liontech->cardEncryptor();
$encryptedCard = $encryptor->encryptForPayment([
    'pan' => '4405639704015096',
    'cvv' => '123',
    'exp_month' => 12,
    'exp_year' => 2030,
    'cardHolder' => 'John Doe',
]);

$paymentRequest = new CreatePaymentRequest(
    amount: new Money('50.00', Currency::USD),
    paymentData: PaymentData::card(new EncryptedCardData(
        encryptedCardData: $encryptedCard['encryptedCardData'],
        cardHolder: $encryptedCard['cardHolder'],
    )),
    customer: new CustomerData(
        email: 'customer@example.com',
        ip: '192.168.1.1',
        fingerprint: 'browser_fingerprint_here',
    ),
    orderId: 'ord_123',
    autoApprove: true,
    backLink: 'https://your-site.com/payment-result',
);

$payment = $liontech->payments()->create($paymentRequest);

if ($payment->requiresRedirect()) {
    // 3DS verification required
    header('Location: ' . $payment->getRedirectUrl());
    exit;
}

if ($payment->isSuccessful()) {
    echo "Payment successful!\n";
}
```

### Issue a Refund

```php
<?php

use Nokimaro\LionTech\Requests\CreateRefundRequest;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

$refundRequest = new CreateRefundRequest(
    amount: new Money('25.00', Currency::USD),
    paymentId: 'pay_123',
    webhookUrl: 'https://your-site.com/webhook',
);

$refund = $liontech->refunds()->create($refundRequest);

echo "Refund ID: {$refund->refundId}\n";
echo "Status: {$refund->status->value}\n";
```

### Handle Webhooks

```php
<?php

use Nokimaro\LionTech\Webhooks\WebhookPayload;

$payload = file_get_contents('php://input');
$headers = getallheaders();

if (! $liontech->webhookVerifier()->verify($headers, $payload)) {
    http_response_code(401);
    exit;
}

$webhook = WebhookPayload::fromJson($payload);
$payment = $webhook->payment;

if ($payment->isSuccessful()) {
    // Payment confirmed — fulfil the order
    echo "Order {$payment->orderId} paid (txn: {$payment->txnId})";
} elseif ($payment->isDeclined()) {
    // Payment declined — notify the customer
    $reason = $webhook->error?->description ?? 'Unknown reason';
    echo "Payment {$payment->paymentId} declined: {$reason}";
}

http_response_code(200);
```

### Token Refresh

```php
<?php

use Nokimaro\LionTech\Requests\RefreshTokenRequest;

$refreshRequest = new RefreshTokenRequest(
    refreshToken: 'your_current_refresh_token',
);

$response = $liontech->auth()->refreshAndApply($refreshRequest);

echo "New access token: {$response->accessToken}\n";
echo "Expires at: {$response->accessTokenExpireAt->format('Y-m-d H:i:s')}\n";
```

## Documentation

### Available Clients

```php
// Authentication & Token Management
$liontech->auth()->refreshTokens($request);
$liontech->auth()->refreshAndApply($request);

// Orders
$liontech->orders()->create($request);
$liontech->orders()->createWithId('custom_id', $request);
$liontech->orders()->get('order_id');
$liontech->orders()->cancel('order_id');
$liontech->orders()->close('order_id');

// Payments
$liontech->payments()->create($request);
$liontech->payments()->createWithId('custom_id', $request);
$liontech->payments()->get('payment_id');
$liontech->payments()->confirm('payment_id');
$liontech->payments()->getRefunds('payment_id');

// Refunds
$liontech->refunds()->create($request);
$liontech->refunds()->createWithId('custom_id', $request);
$liontech->refunds()->get('refund_id');

// Payouts
$liontech->payouts()->createWithId('custom_id', $request);
$liontech->payouts()->get('payout_id');

// Tokens (Saved Payment Methods)
$liontech->tokens()->list(accountId: 'acc_123');
$liontech->tokens()->delete('token_id');

// Balances
$liontech->balances()->list();

// Transfers
$liontech->transfers()->create($data);
$liontech->transfers()->get('transfer_id');

// Signature
$liontech->signature()->getPublicKey();
```

### Security Utilities

```php
// Webhook Signature Verification
$verifier = $liontech->webhookVerifier();          // auto-fetches public key
$verifier = $liontech->webhookVerifier($pemKey);   // or provide your own
$isValid = $verifier->verify($headers, $payload);

// Card Encryption
$encryptor = $liontech->cardEncryptor();           // auto-fetches encryption key
$encryptor = $liontech->cardEncryptor($pemKey);    // or provide your own
$encrypted = $encryptor->encryptForPayment([
    'pan' => '4405639704015096',
    'cvv' => '123',
    'exp_month' => 12,
    'exp_year' => 2030,
    'cardHolder' => 'John Doe',
]);
```

### Custom HTTP Client

The SDK uses [PSR-18](https://www.php-fig.org/psr/psr-18/) so any compatible HTTP client works.

```php
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Nokimaro\LionTech\Client;
use Nokimaro\LionTech\Http\Transport;

$transport = new Transport(
    baseUrl: 'https://api.liontechnology.ai',
    client: new GuzzleClient(),
    requestFactory: new HttpFactory(),
    streamFactory: new HttpFactory(),
);

$liontech = new Client(
    accessToken: 'your_token',
    httpClient: $transport,
);
```

### Sandbox Environment

```php
use Nokimaro\LionTech\Client;

// Via constructor
$liontech = new Client(
    accessToken: 'your_sandbox_token',
    baseUrl: 'https://api.sandbox.liontechnology.ai',
    secureUrl: 'https://secure.sandbox.liontechnology.ai',
);

// Via builder
$liontech = Client::builder()
    ->accessToken('your_sandbox_token')
    ->sandbox()
    ->build();
```

## Error Handling

The SDK throws typed exceptions for different error scenarios:

```php
use Nokimaro\LionTech\Exceptions\Auth\AuthenticationException;
use Nokimaro\LionTech\Exceptions\Auth\TokenExpiredException;
use Nokimaro\LionTech\Exceptions\Business\ConflictException;
use Nokimaro\LionTech\Exceptions\RateLimitException;
use Nokimaro\LionTech\Exceptions\ResourceNotFoundException;
use Nokimaro\LionTech\Exceptions\Transport\TransportException;
use Nokimaro\LionTech\Exceptions\Validation\ValidationException;

try {
    $payment = $liontech->payments()->create($request);
} catch (TokenExpiredException $e) {
    // Token expired — refresh and retry
    $liontech->auth()->refreshAndApply($refreshRequest);
    $payment = $liontech->payments()->create($request);
} catch (ValidationException $e) {
    // Invalid request data
    $errors = $e->getErrors();
} catch (ResourceNotFoundException $e) {
    // Resource doesn't exist
} catch (AuthenticationException $e) {
    // Authentication failed
} catch (RateLimitException $e) {
    // Too many requests
} catch (TransportException $e) {
    // Network or server error
}
```

## Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run static analysis
composer phpstan

# Check code style
composer ecs

# Fix code style
composer fix
```

## Test Cards

The following test cards are available in the sandbox environment:

| Card Number | Scenario |
|-------------|----------|
| `5522 0427 0506 6736` | Payment with 3DS (OTP: `123456`) |
| `4405 6397 0401 5096` | Payment without 3DS |

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover a security vulnerability, please review our [Security Policy](SECURITY.md)
and report it via [GitHub Security Advisories](https://github.com/nokimaro/liontech-php-sdk/security/advisories/new).
**Do not** open a public issue.

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.
