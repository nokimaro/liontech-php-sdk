<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Security;

use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;

final readonly class WebhookSignatureVerifier
{
    private const string SIGNATURE_HEADER = 'X-Payload-Signature';

    private PublicKey $publicKey;

    public function __construct(string $publicKeyPem)
    {
        $key = PublicKeyLoader::load($publicKeyPem);
        assert($key instanceof PublicKey);
        $this->publicKey = $key;
    }

    /**
     * Verify the webhook signature from request headers and body.
     *
     * @param array<string, string|list<string>> $headers Request headers
     * @param string $payload Raw request body
     */
    public function verify(array $headers, string $payload): bool
    {
        $signature = $this->extractSignature($headers);

        if ($signature === null) {
            return false;
        }

        $decoded = base64_decode($signature, true);

        if ($decoded === false) {
            return false;
        }

        $result = $this->publicKey->verify($payload, $decoded);

        return is_bool($result) && $result;
    }

    /**
     * @param array<string, string|list<string>> $headers
     */
    private function extractSignature(array $headers): ?string
    {
        foreach ($headers as $key => $value) {
            if (strtolower($key) === strtolower(self::SIGNATURE_HEADER)) {
                if (is_array($value)) {
                    $first = reset($value);
                    return is_string($first) ? $first : null;
                }

                return $value;
            }
        }

        return null;
    }
}
