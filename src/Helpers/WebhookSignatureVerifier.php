<?php

declare(strict_types=1);

namespace LionTech\SDK\Helpers;

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PublicKey;

final class WebhookSignatureVerifier
{
    private const SIGNATURE_HEADER = 'X-Payload-Signature';

    private readonly PublicKey $publicKey;

    public function __construct(string $publicKeyPem)
    {
        $this->publicKey = RSA::loadPublicKey($publicKeyPem);
    }

    /**
     * Verify the webhook signature from request headers and body.
     *
     * @param array<string, string|array> $headers Request headers
     * @param string $payload Raw request body
     */
    public function verify(array $headers, string $payload): bool
    {
        $signature = $this->extractSignature($headers);

        if ($signature === null) {
            return false;
        }

        return $this->publicKey->verify($payload, base64_decode($signature, true));
    }

    /**
     * @param array<string, string|array> $headers
     */
    private function extractSignature(array $headers): ?string
    {
        foreach ($headers as $key => $value) {
            if (strtolower($key) === strtolower(self::SIGNATURE_HEADER)) {
                return is_array($value) ? reset($value) : $value;
            }
        }

        return null;
    }
}
