<?php

declare(strict_types=1);

/**
 * Example: Webhook Handling
 *
 * This example demonstrates how to:
 * 1. Verify the webhook signature
 * 2. Parse the payload into typed DTOs
 * 3. Handle RECONCILED and DECLINED outcomes
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Nokimaro\LionTech\Client;
use Nokimaro\LionTech\Webhooks\WebhookPayload;

// Initialize the SDK
$liontech = new Client(accessToken: $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here');

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
    echo "Order {$payment->orderId} paid successfully (txn: {$payment->txnId})\n";
} elseif ($payment->isDeclined()) {
    // Payment declined — notify the customer
    $reason = $webhook->error?->description ?? 'Unknown reason';
    echo "Payment {$payment->paymentId} declined: {$reason}\n";
}

http_response_code(200);
