<?php

declare(strict_types=1);

use Nokimaro\LionTech\ValueObjects\CallbackUrl;

it('creates valid callback URL', function (): void {
    $url = new CallbackUrl('https://example.com/callback');

    expect($url->url)
        ->toBe('https://example.com/callback');
});

it('casts to string', function (): void {
    $url = new CallbackUrl('https://example.com/callback');

    expect((string) $url)
        ->toBe('https://example.com/callback');
});

it('serializes to string', function (): void {
    $url = new CallbackUrl('https://example.com/callback');

    expect($url->jsonSerialize())
        ->toBe('https://example.com/callback');
});

it('throws on invalid URL', function (): void {
    new CallbackUrl('not-a-valid-url');
})->throws(InvalidArgumentException::class);

it('is immutable', function (): void {
    $url = new CallbackUrl('https://example.com/callback');
    $reflection = new ReflectionClass($url);

    expect($reflection->isReadOnly())
        ->toBeTrue();
});
