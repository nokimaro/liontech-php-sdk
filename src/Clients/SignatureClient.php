<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Json;

final readonly class SignatureClient
{
    private const string SIGNATURE_KEY_PATH = '/signature-key';

    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    /**
     * Get the signature public key for webhook verification.
     */
    public function getPublicKey(): string
    {
        $response = $this->httpClient->get(self::SIGNATURE_KEY_PATH);
        $data = Json::decode((string) $response->getBody());

        return Json::getString($data, 'pem');
    }
}
