<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Clients;

use Nokimaro\LionTech\Http\Transport;
use Nokimaro\LionTech\Json;

final readonly class SignatureClient
{
    public function __construct(
        private Transport $httpClient,
    ) {
    }

    public function getPublicKey(): string
    {
        $response = $this->httpClient->get('/signature-key');
        $data = Json::decode((string) $response->getBody());

        return Json::getString($data, 'pem');
    }
}
