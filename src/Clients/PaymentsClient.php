<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\ApiClient;
use Nokimaro\LionTech\Json;
use Nokimaro\LionTech\Requests\CreatePaymentRequest;
use Nokimaro\LionTech\Responses\PaymentResponse;
use Nokimaro\LionTech\Responses\RefundResponse;

final readonly class PaymentsClient
{
    public function __construct(
        private ApiClient $apiClient,
    ) {
    }

    public function create(CreatePaymentRequest $request): PaymentResponse
    {
        $response = $this->apiClient->post('/api/v1/merchant/payments', $request);

        return PaymentResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function createWithId(string $paymentId, CreatePaymentRequest $request): PaymentResponse
    {
        $response = $this->apiClient->put('/api/v1/merchant/payments/' . $paymentId, $request);

        return PaymentResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function get(string $paymentId): PaymentResponse
    {
        $response = $this->apiClient->get('/api/v1/merchant/payments/' . $paymentId);

        return PaymentResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    public function confirm(string $paymentId): PaymentResponse
    {
        $response = $this->apiClient->post('/api/v1/merchant/payments/' . $paymentId . '/confirm');

        return PaymentResponse::fromArray(Json::decode((string) $response->getBody()));
    }

    /**
     * @return list<RefundResponse>
     */
    public function getRefunds(string $paymentId): array
    {
        $response = $this->apiClient->get('/api/v1/merchant/payments/' . $paymentId . '/refunds');
        $data = Json::decode((string) $response->getBody());

        return array_map(RefundResponse::fromArray(...), Json::assertArrayOfArrays($data['items'] ?? $data));
    }
}
