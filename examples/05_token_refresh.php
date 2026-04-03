<?php

declare(strict_types=1);

/**
 * Example: Token Refresh
 *
 * This example demonstrates how to:
 * 1. Refresh access tokens
 * 2. Apply new tokens to the SDK
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LionTech\SDK\DTOs\Request\RefreshTokenRequest;
use LionTech\SDK\LionTechSDK;

// Initialize the SDK with refresh token
$sdk = new LionTechSDK([
    'access_token' => $_ENV['LIONTECH_ACCESS_TOKEN'] ?? 'your_access_token_here',
]);

$refreshRequest = new RefreshTokenRequest(
    refreshToken: $_ENV['LIONTECH_REFRESH_TOKEN'] ?? 'your_refresh_token_here',
);

try {
    // Refresh tokens and automatically apply them to the SDK
    $response = $sdk->auth()
        ->refreshAndApply($refreshRequest);

    echo "Tokens refreshed successfully!\n";
    echo "New Access Token: {$response->accessToken}\n";
    echo "Access Token Expires At: {$response->accessTokenExpireAt->format('Y-m-d H:i:s')}\n";
    echo "New Refresh Token: {$response->refreshToken}\n";
    echo "Refresh Token Expires At: {$response->refreshTokenExpireAt->format('Y-m-d H:i:s')}\n";

    // IMPORTANT: Store the new refresh token securely for future use
    // The old refresh token is now invalid
    // file_put_contents('.env.local', "LIONTECH_REFRESH_TOKEN={$response->refreshToken}\n", FILE_APPEND);

} catch (\Exception $e) {
    echo "Error refreshing tokens: {$e->getMessage()}\n";
}
