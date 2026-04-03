<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\Http\HttpClient;

final class SignatureClient
{
    private const SIGNATURE_KEY_PATH = '/signature-key';

    public function __construct(
        private readonly HttpClient $httpClient,
    ) {}

    /**
     * Get the signature public key for webhook verification.
     */
    public function getPublicKey(): string
    {
        $response = $this->httpClient->get(self::SIGNATURE_KEY_PATH);
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $data['pem'];
    }
}
