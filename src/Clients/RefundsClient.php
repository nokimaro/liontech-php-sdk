<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\CreateRefundRequest;
use LionTech\SDK\DTOs\Response\RefundResponse;
use LionTech\SDK\Http\HttpClient;

final readonly class RefundsClient
{
    private const string REFUNDS_PATH = '/api/v1/merchant/refunds';

    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    /**
     * Create a new refund with a PSP-generated ID.
     */
    public function create(CreateRefundRequest $request): RefundResponse
    {
        $response = $this->httpClient->post(self::REFUNDS_PATH, $request);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return RefundResponse::fromArray($data);
    }

    /**
     * Create a refund with a merchant-provided ID.
     */
    public function createWithId(string $refundId, CreateRefundRequest $request): RefundResponse
    {
        $response = $this->httpClient->put(self::REFUNDS_PATH . '/' . $refundId, $request);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return RefundResponse::fromArray($data);
    }

    /**
     * Get refund information.
     */
    public function get(string $refundId): RefundResponse
    {
        $response = $this->httpClient->get(self::REFUNDS_PATH . '/' . $refundId);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return RefundResponse::fromArray($data);
    }
}
