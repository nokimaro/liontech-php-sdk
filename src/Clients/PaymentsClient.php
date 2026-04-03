<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\DTOs\Request\CreatePaymentRequest;
use LionTech\SDK\DTOs\Response\PaymentResponse;
use LionTech\SDK\DTOs\Response\RefundResponse;
use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Json;

final readonly class PaymentsClient
{
    private const string PAYMENTS_PATH = '/api/v1/merchant/payments';

    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    /**
     * Create a new payment with a PSP-generated ID.
     */
    public function create(CreatePaymentRequest $request): PaymentResponse
    {
        $response = $this->httpClient->post(self::PAYMENTS_PATH, $request);
        $data = Json::decode((string) $response->getBody());

        return PaymentResponse::fromArray($data);
    }

    /**
     * Create a payment with a merchant-provided ID.
     */
    public function createWithId(string $paymentId, CreatePaymentRequest $request): PaymentResponse
    {
        $response = $this->httpClient->put(self::PAYMENTS_PATH . '/' . $paymentId, $request);
        $data = Json::decode((string) $response->getBody());

        return PaymentResponse::fromArray($data);
    }

    /**
     * Get payment information.
     */
    public function get(string $paymentId): PaymentResponse
    {
        $response = $this->httpClient->get(self::PAYMENTS_PATH . '/' . $paymentId);
        $data = Json::decode((string) $response->getBody());

        return PaymentResponse::fromArray($data);
    }

    /**
     * Confirm an authorized payment.
     */
    public function confirm(string $paymentId): PaymentResponse
    {
        $response = $this->httpClient->post(self::PAYMENTS_PATH . '/' . $paymentId . '/confirm');
        $data = Json::decode((string) $response->getBody());

        return PaymentResponse::fromArray($data);
    }

    /**
     * List refunds for a payment.
     *
     * @return array<int, RefundResponse>
     */
    public function getRefunds(string $paymentId): array
    {
        $response = $this->httpClient->get(self::PAYMENTS_PATH . '/' . $paymentId . '/refunds');
        $data = Json::decode((string) $response->getBody());

        $items = Json::assertArrayOfArrays($data['items'] ?? $data);

        $refunds = [];
        foreach ($items as $item) {
            $refunds[] = RefundResponse::fromArray($item);
        }

        return $refunds;
    }
}
