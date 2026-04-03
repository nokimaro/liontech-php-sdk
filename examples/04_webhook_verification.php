<?php

declare(strict_types=1);

/**
 * Example: Webhook Signature Verification
 *
 * This example demonstrates how to:
 * 1. Verify webhook signatures
 * 2. Process webhook payloads securely
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LionTech\SDK\LionTechSDK;

// Initialize the SDK
$sdk = new LionTechSDK([
    'access_token' => $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here',
]);

// Get the raw webhook payload
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Verify the webhook signature
$verifier = $sdk->webhookVerifier();

try {
    if ($verifier->verify($headers, $payload)) {
        // Signature is valid, process the webhook
        $data = json_decode($payload, true);

        echo "Webhook signature verified successfully!\n";
        echo 'Payload: ' . json_encode($data, JSON_PRETTY_PRINT) . "\n";

        // Process the payment status update
        // Example: Update your database with the payment status
        // if ($data['status']['value'] === 'RECONCILED') {
        //     // Payment successful, fulfill the order
        // }

        // Respond with 200 OK to acknowledge receipt
        http_response_code(200);
        echo "Webhook processed successfully\n";
    } else {
        // Invalid signature, reject the request
        http_response_code(401);
        echo "Invalid webhook signature\n";
    }
} catch (\Exception $e) {
    // Error during verification
    http_response_code(500);
    echo "Error verifying webhook: {$e->getMessage()}\n";
}
