---
name: Bug Report
about: Create a report to help us improve
title: "CardEncryptor: PublicKey::setEncryptionMode() method does not exist"
labels: bug
assignees: ""
---

## Describe the bug

`CardEncryptor` crashes during construction when given an RSA public key PEM string. The error occurs because `phpseclib3\Crypt\RSA\PublicKey` does not have a `setEncryptionMode()` method, but `CardEncryptor` tries to call it.

## Error

```
Error: Call to undefined method phpseclib3\Crypt\RSA\PublicKey::setEncryptionMode()

at src/Helpers/CardEncryptor.php:20
     16▕     ) {
     17▕         $key = PublicKeyLoader::load($this->publicKeyPem);
     18▕         assert($key instanceof RSA);
     19▕         $this->rsa = $key;
  ➜  20▕         $this->rsa->setEncryptionMode(RSA::ENCRYPTION_OAEP);
     21▕         $this->rsa->setHash('sha256');
     22▕         $this->rsa->setMGFHash('sha256');
     23▕     }
```

## To Reproduce

```php
use LionTech\SDK\Helpers\CardEncryptor;

$publicKey = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtCUlR9EprWfVac8FpPB7
m6aiJiXOf07+eCyN66Agudkh5QcUps43e+2ogtC9obMdr3xphaKK61bGARN05c0F
A22mBrufrS46TPZhYYeMzcPAas6SuasaUL8JuphXRQjjQrvxJBr43VK9y3Y3QfHu
jKb32aJlS5Ev130zgLQCukmYLh6WmuPcjWuw7V/3gQzTNENjR4VAQYr0pYmHBsy2
d+D/bZCSyRXQ58kbt55Evo+Sjvdnj3wvTrG+i5R1borWiTWzduLDcd/MO83byLyM
K0GwJprh7j/U+NSJHfLpi8kiuih6R41wNf2BWUEKo6J8vaBFPQL2iJ4ixvB2sxIx
KwIDAQAB
-----END PUBLIC KEY-----";

$encryptor = new CardEncryptor($publicKey); // 💥 Crashes here
```

## Expected behavior

`CardEncryptor` should accept a public key and be able to encrypt card data using RSA-OAEP-256.

## Root cause

In phpseclib3, `PublicKey` (returned by `PublicKeyLoader::load()` for public key PEMs) does **not** have `setEncryptionMode()`, `setHash()`, or `setMGFHash()` methods. These methods exist on the `RSA` class itself, not on the `PublicKey` subclass.

```
PublicKeyLoader::load($pem)
  → returns phpseclib3\Crypt\RSA\PublicKey
  → PublicKey extends AsymmetricKey
  → PublicKey does NOT have setEncryptionMode()
```

## Environment

- **PHP SDK version**: dev-master
- **PHP version**: 8.3, 8.4, 8.5 (all affected)
- **phpseclib version**: ^3.0

## Impact

This bug makes `CardEncryptor` completely unusable. Any code path that instantiates `CardEncryptor` will crash.

## Suggested fix

### Option A: Use phpseclib3's fluent API

```php
use phpseclib3\Crypt\RSA;

public function __construct(string $publicKeyPem)
{
    $key = RSA::loadPublicKey($publicKeyPem);
    $this->rsa = $key->withPadding(RSA::ENCRYPTION_OAEP)
                     ->withHash('sha256')
                     ->withMGFHash('sha256');
}
```

### Option B: Check type before calling methods

```php
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\Common\AsymmetricKey;

public function __construct(string $publicKeyPem)
{
    $key = PublicKeyLoader::load($publicKeyPem);
    assert($key instanceof RSA);

    // Use the fluent API which works on PublicKey
    $this->rsa = $key->withPadding(RSA::ENCRYPTION_OAEP)
                     ->withHash('sha256')
                     ->withMGFHash('sha256');
}
```

The key insight is that phpseclib3 uses `withPadding()` / `withHash()` / `withMGFHash()` (which return new immutable instances) rather than `setEncryptionMode()` / `setHash()` / `setMGFHash()` (mutators).

## Test case

```php
it('can create CardEncryptor with public key', function () {
    $publicKey = file_get_contents(__DIR__ . '/fixtures/test-public-key.pem');
    $encryptor = new CardEncryptor($publicKey);

    expect($encryptor)->toBeInstanceOf(CardEncryptor::class);
});
```

## Additional context

Discovered while building the Laravel SDK wrapper package. The `WebhookSignatureVerifier` works correctly with the same public key pattern — only `CardEncryptor` is affected.
