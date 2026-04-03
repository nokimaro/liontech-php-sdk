<?php

declare(strict_types=1);

/**
 * Example: Basic SDK Setup and Order Creation
 *
 * This example demonstrates how to:
 * 1. Initialize the SDK
 * 2. Create an order
 * 3. Retrieve order information
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LionTech\SDK\DTOs\Request\CreateOrderRequest;
use LionTech\SDK\DTOs\Request\CustomerData;
use LionTech\SDK\LionTechSDK;
use LionTech\SDK\ValueObjects\Currency;
use LionTech\SDK\ValueObjects\Money;

// Initialize the SDK
$sdk = new LionTechSDK([
    'access_token' => $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here',
    // For sandbox environment:
    // 'base_url' => 'https://api.sandbox.liontechnology.ai',
    // 'secure_url' => 'https://secure.sandbox.liontechnology.ai',
]);

// Create an order
$orderRequest = new CreateOrderRequest(
    amount: new Money('100.00', Currency::USD),
    customer: new CustomerData(email: 'customer@example.com', fullName: 'John Doe', ip: '192.168.1.1'),
    autoApprove: true,
    declineUrl: 'https://your-site.com/decline',
    successUrl: 'https://your-site.com/success',
    webhookUrl: 'https://your-site.com/webhook',
    description: 'Order #12345',
);

try {
    $order = $sdk->orders()
        ->create($orderRequest);

    echo "Order created successfully!\n";
    echo "Order ID: {$order->orderId}\n";
    echo "Amount: {$order->amount->amount} {$order->amount->currency->value}\n";
    echo "Status: {$order->status->value}\n";

    if ($order->payUrl !== null) {
        echo "Payment URL: {$order->payUrl}\n";
    }

    // Retrieve the order
    $retrievedOrder = $sdk->orders()
        ->get($order->orderId);
    echo "\nRetrieved order: {$retrievedOrder->orderId}\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
