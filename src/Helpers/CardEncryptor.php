<?php

declare(strict_types=1);

namespace LionTech\SDK\Helpers;

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

        // phpseclib3 PublicKey uses immutable fluent API for configuration
        $this->rsa = $key->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha256')
            ->withMGFHash('sha256');
    }

    /**
     * Encrypt card data using RSA-OAEP-256.
     *
     * @param array{pan: string, cvv: string, exp_month: int, exp_year: int, cardHolder?: string} $cardData
     * @return string Base64-encoded encrypted card data
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
     * @param array{pan: string, cvv: string, exp_month: int, exp_year: int, cardHolder?: string} $cardData
     * @return array{encryptedCardData: string, cardHolder?: string}
     */
    public function encryptForPayment(array $cardData): array
    {
        $cardHolder = $cardData['cardHolder'] ?? null;
        $encrypted = $this->encrypt($cardData);

        $result = [
            'encryptedCardData' => $encrypted,
        ];

        if ($cardHolder !== null) {
            $result['cardHolder'] = $cardHolder;
        }

        return $result;
    }
}
