<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\Transport;
use Nokimaro\LionTech\Json;

final readonly class EncryptionKeyClient
{
    public function __construct(
        private Transport $httpClient,
    ) {
    }

    /**
     * Fetch the RSA-OAEP-256 public key used for encrypting card data.
     *
     * Returns the PEM-encoded public key from the /encryption-key endpoint.
     */
    public function getPublicKey(): string
    {
        $response = $this->httpClient->get('/encryption-key');
        $data = Json::decode((string) $response->getBody());

        return Json::getString($data, 'pem');
    }
}
