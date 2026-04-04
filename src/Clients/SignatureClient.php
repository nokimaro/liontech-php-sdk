<?php

declare(strict_types=1);

namespace LionTech\SDK\Clients;

use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Json;

final readonly class SignatureClient
{
    public function __construct(
        private HttpClient $httpClient,
    ) {
    }

    public function getPublicKey(): string
    {
        $response = $this->httpClient->get('/signature-key');
        $data = Json::decode((string) $response->getBody());

        return Json::getString($data, 'pem');
    }
}
