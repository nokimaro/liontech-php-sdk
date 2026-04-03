<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Response\SavedPaymentMethod;
use LionTech\SDK\Http\HttpClient;

final class TokensClient
{
    private const TOKENS_PATH = '/api/v1/merchant/tokens';

    public function __construct(
        private readonly HttpClient $httpClient,
    ) {}

    /**
     * List saved payment tokens.
     *
     * @return array<int, SavedPaymentMethod>
     */
    public function list(
        ?string $accountId = null,
        ?string $email = null,
        ?string $phone = null,
    ): array {
        $query = array_filter([
            'accountId' => $accountId,
            'email' => $email,
            'phone' => $phone,
        ], static fn (?string $value): bool => $value !== null);

        $response = $this->httpClient->get(self::TOKENS_PATH, $query);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $methods = [];
        foreach ($data['saved_payment_methods'] ?? $data['items'] ?? [] as $item) {
            $methods[] = SavedPaymentMethod::fromArray($item);
        }

        return $methods;
    }

    /**
     * Delete a saved payment token.
     */
    public function delete(string $tokenId): void
    {
        $this->httpClient->delete(self::TOKENS_PATH . '/' . $tokenId);
    }
}
