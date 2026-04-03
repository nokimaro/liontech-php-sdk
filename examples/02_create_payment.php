<?php

declare(strict_types=1);

/**
 * Example: Payment with Encrypted Card Data
 *
 * This example demonstrates how to:
 * 1. Encrypt card data
 * 2. Create a payment
 * 3. Handle 3DS redirect if required
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LionTech\SDK\DTOs\Request\CreatePaymentRequest;
use LionTech\SDK\DTOs\Request\CustomerData;
use LionTech\SDK\LionTechSDK;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\EncryptedCardData;
use LionTech\SDK\ValueObjects\Money;
use LionTech\SDK\ValueObjects\PaymentData;

// Initialize the SDK
$sdk = new LionTechSDK([
    'access_token' => $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here',
]);

// Encrypt card data
$encryptor = $sdk->cardEncryptor();
$encryptedCard = $encryptor->encryptForPayment([
    'pan' => '4405639704015096', // Test card (no 3DS)
    'cvv' => '123',
    'exp_month' => 12,
    'exp_year' => 2030,
    'cardHolder' => 'John Doe',
]);

// Create the payment
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
    autoApprove: true,
    backLink: 'https://your-site.com/payment-result',
);

try {
    $payment = $sdk->payments()
        ->create($paymentRequest);

    echo "Payment created!\n";
    echo "Payment ID: {$payment->paymentId}\n";
    echo "Status: {$payment->status->value}\n";

    // Check if 3DS redirect is required
    if ($payment->requiresRedirect()) {
        echo "\n3DS verification required. Redirect to: {$payment->getRedirectUrl()}\n";
        // In a real application, you would redirect the user here
        // header('Location: ' . $payment->getRedirectUrl());
    }

    // Check payment status
    if ($payment->isSuccessful()) {
        echo "\nPayment was successful!\n";
    } elseif ($payment->status->isDeclined()) {
        echo "\nPayment was declined.\n";
    } else {
        echo "\nPayment is still pending.\n";
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
