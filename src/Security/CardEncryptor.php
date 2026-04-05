<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Security;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;

final readonly class CardEncryptor
{
    private RSA $rsa;

    public function __construct(
        private string $publicKeyPem
    ) {
        $key = PublicKeyLoader::load($this->publicKeyPem);
        assert($key instanceof RSA);

        // RSA-OAEP-256: OAEP with SHA-256 hash and SHA-256 MGF1 (per RFC 7518 §4.3)
        $this->rsa = $key
            ->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha256')
            ->withMGFHash('sha256');
    }

    /**
     * Encrypt card data using RSA-OAEP-256 (SHA-256 hash + MGF1-SHA-256).
     *
     * The server uses the WebCrypto API: crypto.subtle.decrypt({name:"RSA-OAEP"}, key, bytes),
     * so this returns raw RSA-OAEP-256 output encoded as base64 — not a JWE structure.
     *
     * @param array{pan: string, cvv: string, exp_month: int, exp_year: int, cardHolder?: string|null} $cardData
     * @return string Base64-encoded RSA-OAEP-256 encrypted card data
     */
    public function encrypt(array $cardData): string
    {
        $json = json_encode($cardData, JSON_THROW_ON_ERROR);
        $encrypted = $this->rsa->encrypt($json);
        assert(is_string($encrypted));

        return base64_encode($encrypted);
    }

    /**
     * Encrypt card data and return in the format expected by payment requests.
     *
     * @param array{pan: string, cvv: string, exp_month: int, exp_year: int, cardHolder?: string|null} $cardData
     * @return array{encryptedCardData: string, cardHolder?: string}
     */
    public function encryptForPayment(array $cardData): array
    {
        $cardHolder = $cardData['cardHolder'] ?? null;
        $encrypted = $this->encrypt($cardData);

        $result = [
            'encryptedCardData' => $encrypted,
        ];

        if (is_string($cardHolder)) {
            $result['cardHolder'] = $cardHolder;
        }

        return $result;
    }
}
