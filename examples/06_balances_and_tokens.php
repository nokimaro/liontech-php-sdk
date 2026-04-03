<?php

declare(strict_types=1);

/**
 * Example: Balances and Saved Payment Methods
 *
 * This example demonstrates how to:
 * 1. Retrieve account balances
 * 2. List saved payment methods
 * 3. Delete saved payment methods
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LionTech\SDK\LionTechSDK;

// Initialize the SDK
$sdk = new LionTechSDK([
    'access_token' => $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here',
]);

try {
    // Get account balances
    echo "Account Balances:\n";
    echo str_repeat('-', 50) . "\n";

    $balances = $sdk->balances()
        ->list();

    foreach ($balances as $account) {
        echo "Account ID: {$account->accountId}\n";
        echo "Currency: {$account->currency->value} ({$account->currency->symbol()})\n";
        echo "Balance: {$account->balance}\n";
        echo "Updated: {$account->updatedAt->format('Y-m-d H:i:s')}\n";
        echo str_repeat('-', 50) . "\n";
    }

    // List saved payment methods
    echo "\nSaved Payment Methods:\n";
    echo str_repeat('-', 50) . "\n";

    $tokens = $sdk->tokens()
        ->list(accountId: 'acc_123');

    foreach ($tokens as $token) {
        echo "Token ID: {$token->tokenId}\n";
        echo "Payment Method ID: {$token->paymentMethodId}\n";
        echo "Display: {$token->displayValue}\n";
        echo "Card Type: {$token->cardType}\n";
        echo "Expiration: {$token->cardExp}\n";
        echo 'Requires CVV: ' . ($token->cardRequiresCvv ? 'Yes' : 'No') . "\n";
        echo str_repeat('-', 50) . "\n";
    }

    // Delete a saved payment method
    // $sdk->tokens()->delete('token_id_here');
    // echo "Token deleted successfully\n";

} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
