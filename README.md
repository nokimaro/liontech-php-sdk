# LionTech PHP SDK

[![Tests](https://github.com/nokimaro/liontech-php-sdk/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/nokimaro/liontech-php-sdk/actions/workflows/ci.yml)
![Coverage](https://img.shields.io/badge/Coverage-96%25-2ECC71?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php)
[![License](https://img.shields.io/badge/license-MIT-blue?style=flat-square)](LICENSE.md)

Community-maintained PHP SDK for the [LionTech Payment Gateway](https://liontechnology.ai). This SDK provides a type-safe, domain-oriented interface for integrating LionTech's payment processing capabilities into your PHP applications.

## Features

- ✅ **Complete API Coverage**: Orders, Payments, Refunds, Payouts, Tokens, Transfers, Balances
- ✅ **Type-Safe**: Strong typing with PHP 8.3 enums, readonly classes, and typed properties
- ✅ **PSR Compliant**: PSR-4, PSR-7, PSR-17, PSR-18 compatible
- ✅ **Secure**: Webhook signature verification and RSA card encryption helpers
- ✅ **Token Management**: Automatic token refresh support
- ✅ **Domain-Oriented**: Clean API with request/response DTOs instead of raw arrays
- ✅ **Extensible**: Bring your own PSR-18 HTTP client

## Requirements

- PHP 8.3 or higher
- PSR-18 HTTP Client implementation (Guzzle recommended)

## Installation

Install the SDK via Composer:

```bash
composer require nokimaro/liontech-php-sdk
```

If you don't have an HTTP client installed, we recommend Guzzle:

```bash
composer require guzzlehttp/guzzle
```

## Quick Start

### Basic Setup

```php
<?php

require_once 'vendor/autoload.php';

use LionTech\SDK\LionTechSDK;

$sdk = new LionTechSDK([
    'access_token' => 'your_access_token_here',
    // Optional: 'refresh_token' => 'your_refresh_token',
    // Optional: 'base_url' => 'https://api.sandbox.liontechnology.ai',
]);
```

### Create an Order

```php
<?php

use LionTech\SDK\DTOs\Request\CreateOrderRequest;
use LionTech\SDK\DTOs\Request\CustomerData;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

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

$order = $sdk->orders()->create($orderRequest);

echo "Order ID: {$order->orderId}\n";
echo "Pay URL: {$order->payUrl}\n";
```

### Process a Payment with Encrypted Card Data

```php
<?php

use LionTech\SDK\DTOs\Request\CreatePaymentRequest;
use LionTech\SDK\DTOs\Request\CustomerData;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\EncryptedCardData;
use LionTech\SDK\ValueObjects\Money;
use LionTech\SDK\ValueObjects\PaymentData;

// First, encrypt the card data
$encryptor = $sdk->cardEncryptor();
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

$payment = $sdk->payments()->create($paymentRequest);

if ($payment->requiresRedirect()) {
    // 3DS verification required
    header('Location: ' . $payment->getRedirectUrl());
    exit;
}

if ($payment->isSuccessful()) {
    echo "Payment successful!\n";
} else {
    echo "Payment pending or declined.\n";
}
```

### Issue a Refund

```php
<?php

use LionTech\SDK\DTOs\Request\CreateRefundRequest;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

$refundRequest = new CreateRefundRequest(
    amount: new Money('25.00', Currency::USD),
    paymentId: 'pay_123',
    webhookUrl: 'https://your-site.com/webhook',
);

$refund = $sdk->refunds()->create($refundRequest);

echo "Refund ID: {$refund->refundId}\n";
echo "Status: {$refund->status->value}\n";
```

### Verify Webhook Signatures

```php
<?php

// In your webhook endpoint
$payload = file_get_contents('php://input');
$headers = getallheaders();

$verifier = $sdk->webhookVerifier();

if ($verifier->verify($headers, $payload)) {
    // Signature is valid, process the webhook
    $data = json_decode($payload, true);
    
    // Process the payment status update
    // ...
    
    http_response_code(200);
} else {
    // Invalid signature, reject the request
    http_response_code(401);
    echo "Invalid webhook signature";
}
```

### Token Refresh

```php
<?php

use LionTech\SDK\DTOs\Request\RefreshTokenRequest;

$refreshRequest = new RefreshTokenRequest(
    refreshToken: 'your_current_refresh_token',
);

$response = $sdk->auth()->refreshAndApply($refreshRequest);

echo "New access token: {$response->accessToken}\n";
echo "New refresh token: {$response->refreshToken}\n";
echo "Expires at: {$response->accessTokenExpireAt->format('Y-m-d H:i:s')}\n";
```

## Documentation

### Available Clients

The SDK provides domain-oriented clients for each API resource:

```php
// Authentication & Token Management
$sdk->auth()->refreshTokens($request);
$sdk->auth()->refreshAndApply($request);

// Orders
$sdk->orders()->create($request);
$sdk->orders()->createWithId('custom_id', $request);
$sdk->orders()->get('order_id');
$sdk->orders()->cancel('order_id');
$sdk->orders()->close('order_id');

// Payments
$sdk->payments()->create($request);
$sdk->payments()->createWithId('custom_id', $request);
$sdk->payments()->get('payment_id');
$sdk->payments()->confirm('payment_id');
$sdk->payments()->getRefunds('payment_id');

// Refunds
$sdk->refunds()->create($request);
$sdk->refunds()->createWithId('custom_id', $request);
$sdk->refunds()->get('refund_id');

// Payouts
$sdk->payouts()->createWithId('custom_id', $request);
$sdk->payouts()->get('payout_id');

// Tokens (Saved Payment Methods)
$sdk->tokens()->list(accountId: 'acc_123');
$sdk->tokens()->delete('token_id');

// Balances
$sdk->balances()->list();

// Transfers
$sdk->transfers()->create($data);
$sdk->transfers()->get('transfer_id');

// Signature
$sdk->signature()->getPublicKey();
```

### Security Helpers

```php
// Webhook Signature Verification
$verifier = $sdk->webhookVerifier(); // Auto-fetches public key
$verifier = $sdk->webhookVerifier($customPemKey); // Or provide your own
$isValid = $verifier->verify($headers, $payload);

// Card Encryption
$encryptor = $sdk->cardEncryptor(); // Auto-fetches encryption key
$encryptor = $sdk->cardEncryptor($customPemKey); // Or provide your own
$encrypted = $encryptor->encryptForPayment([
    'pan' => '4405639704015096',
    'cvv' => '123',
    'exp_month' => 12,
    'exp_year' => 2030,
    'cardHolder' => 'John Doe',
]);
```

### Custom HTTP Client

You can provide your own PSR-18 client:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

$guzzle = new Client();
$factory = new HttpFactory();

$sdk = new LionTechSDK([
    'access_token' => 'your_token',
    'client' => $guzzle,
    'request_factory' => $factory,
    'stream_factory' => $factory,
]);
```

### Sandbox Environment

```php
$sdk = new LionTechSDK([
    'access_token' => 'your_sandbox_token',
    'base_url' => 'https://api.sandbox.liontechnology.ai',
    'secure_url' => 'https://secure.sandbox.liontechnology.ai',
]);
```

## Error Handling

The SDK throws typed exceptions for different error scenarios:

```php
use LionTech\SDK\Exceptions\Auth\AuthenticationException;
use LionTech\SDK\Exceptions\Auth\TokenExpiredException;
use LionTech\SDK\Exceptions\Validation\ValidationException;
use LionTech\SDK\Exceptions\ResourceNotFoundException;
use LionTech\SDK\Exceptions\Business\ConflictException;
use LionTech\SDK\Exceptions\RateLimitException;
use LionTech\SDK\Exceptions\Transport\TransportException;

try {
    $payment = $sdk->payments()->create($request);
} catch (TokenExpiredException $e) {
    // Token expired, refresh and retry
    $sdk->auth()->refreshAndApply($refreshRequest);
    $payment = $sdk->payments()->create($request);
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
composer test:coverage

# Run static analysis
composer phpstan

# Check code style
composer ecs

# Fix code style
composer fix
```

## Test Cards

The following test cards are available in the sandbox environment:

- `5522 0427 0506 6736` — Payment with 3DS (3DS OTP: `123456`)
- `4405 6397 0401 5096` — Payment without 3DS

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

- Documentation: [docs/api.json](docs/api.json)
- Issues: [GitHub Issues](https://github.com/nokimaro/liontech-php-sdk/issues)
- Repository: [nokimaro/liontech-php-sdk](https://github.com/nokimaro/liontech-php-sdk)
