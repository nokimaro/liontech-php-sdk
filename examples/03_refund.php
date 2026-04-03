<?php

declare(strict_types=1);

/**
 * Example: Refund Processing
 *
 * This example demonstrates how to:
 * 1. Create a refund
 * 2. Retrieve refund information
 * 3. List refunds for a payment
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LionTech\SDK\DTOs\Request\CreateRefundRequest;
use LionTech\SDK\LionTechSDK;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

// Initialize the SDK
$sdk = new LionTechSDK([
    'access_token' => $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here',
]);

// Create a refund
$refundRequest = new CreateRefundRequest(
    amount: new Money('25.00', Currency::USD),
    paymentId: 'pay_123', // Replace with actual payment ID
    webhookUrl: 'https://your-site.com/webhook',
);

try {
    $refund = $sdk->refunds()
        ->create($refundRequest);

    echo "Refund created!\n";
    echo "Refund ID: {$refund->refundId}\n";
    echo "Payment ID: {$refund->paymentId}\n";
    echo "Amount: {$refund->amount->amount} {$refund->amount->currency->value}\n";
    echo "Status: {$refund->status->value}\n";

    // Retrieve the refund
    $retrievedRefund = $sdk->refunds()
        ->get($refund->refundId);
    echo "\nRetrieved refund: {$retrievedRefund->refundId}\n";

    // List all refunds for the payment
    $refunds = $sdk->payments()
        ->getRefunds('pay_123');
    echo "\nTotal refunds for payment: " . count($refunds) . "\n";

    foreach ($refunds as $r) {
        echo "- Refund {$r->refundId}: {$r->status->value}\n";
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
