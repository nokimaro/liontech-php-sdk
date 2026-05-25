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

use Nokimaro\LionTech\Client;
use Nokimaro\LionTech\Requests\CreateOrderRequest;
use Nokimaro\LionTech\Requests\CustomerData;
use Nokimaro\LionTech\ValueObjects\Currency;
use Nokimaro\LionTech\ValueObjects\Money;

// Initialize the SDK
$liontech = new Client([
    'access_token' => $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here',
    // For sandbox environment:
    // 'base_url' => 'https://api.sandbox.fusionpayments.io',
    // 'secure_url' => 'https://secure.sandbox.fusionpayments.io',
]);

// Create an order
$orderRequest = new CreateOrderRequest(
    amount: new Money('100.00', Currency::USD),
    description: 'Order #12345',
    customer: new CustomerData(email: 'customer@example.com', fullName: 'John Doe', ip: '192.168.1.1'),
    autoApprove: true,
    declineUrl: 'https://your-site.com/decline',
    successUrl: 'https://your-site.com/success',
    webhookUrl: 'https://your-site.com/webhook',
);

try {
    $order = $liontech->orders()
        ->create($orderRequest);

    echo "Order created successfully!\n";
    echo "Order ID: {$order->orderId}\n";
    echo "Amount: {$order->amount->amount} {$order->amount->currency->value}\n";
    echo "Status: {$order->status->value}\n";

    if ($order->payUrl !== null) {
        echo "Payment URL: {$order->payUrl}\n";
    }

    // Retrieve the order
    $retrievedOrder = $liontech->orders()
        ->get($order->orderId);
    echo "\nRetrieved order: {$retrievedOrder->orderId}\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
