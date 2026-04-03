<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\Http\HttpClient;

final class TransfersClient
{
    private const TRANSFERS_PATH = '/api/v1/merchant/transfers';

    public function __construct(
        private readonly HttpClient $httpClient,
    ) {}

    /**
     * Create a new transfer.
     *
     * @param array<string, mixed> $data Transfer data
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post(self::TRANSFERS_PATH, $data);

        return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get transfer information.
     */
    public function get(string $transferId): array
    {
        $response = $this->httpClient->get(self::TRANSFERS_PATH . '/' . $transferId);

        return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
