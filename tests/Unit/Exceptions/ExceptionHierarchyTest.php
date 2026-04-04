<?php

declare(strict_types=1);

use Nokimaro\LionTech\Exceptions\Auth\AuthenticationException;
use Nokimaro\LionTech\Exceptions\Auth\TokenExpiredException;
use Nokimaro\LionTech\Exceptions\Business\ConflictException;
use Nokimaro\LionTech\Exceptions\RateLimitException;
use Nokimaro\LionTech\Exceptions\ResourceNotFoundException;
use Nokimaro\LionTech\Exceptions\SdkException;
use Nokimaro\LionTech\Exceptions\Transport\TransportException;
use Nokimaro\LionTech\Exceptions\Validation\ValidationException;

it('has correct exception hierarchy', function (): void {
    expect(new AuthenticationException())
        ->toBeInstanceOf(SdkException::class);
    expect(new TokenExpiredException())
        ->toBeInstanceOf(AuthenticationException::class);
    expect(new ValidationException())
        ->toBeInstanceOf(SdkException::class);
    expect(new ConflictException())
        ->toBeInstanceOf(SdkException::class);
    expect(new TransportException())
        ->toBeInstanceOf(SdkException::class);
    expect(new ResourceNotFoundException())
        ->toBeInstanceOf(SdkException::class);
    expect(new RateLimitException())
        ->toBeInstanceOf(SdkException::class);
});

it('creates authentication exception with default message', function (): void {
    $exception = new AuthenticationException();

    expect($exception->getMessage())
        ->toBe('Authentication failed');
    expect($exception->getCode())
        ->toBe(0);
});

it('creates token expired exception with default code', function (): void {
    $exception = new TokenExpiredException();

    expect($exception->getMessage())
        ->toBe('Access token has expired');
    expect($exception->getCode())
        ->toBe(504);
});

it('creates validation exception with errors', function (): void {
    $errors = [
        'field1' => 'is required',
        'field2' => 'is invalid',
    ];

    $exception = new ValidationException(message: 'Validation failed', code: 400, errors: $errors);

    expect($exception->getMessage())
        ->toBe('Validation failed');
    expect($exception->getCode())
        ->toBe(400);
    expect($exception->getErrors())
        ->toBe($errors);
});

it('creates resource not found exception', function (): void {
    $exception = new ResourceNotFoundException('Order not found', 404);

    expect($exception->getMessage())
        ->toBe('Order not found');
    expect($exception->getCode())
        ->toBe(404);
});

it('creates rate limit exception', function (): void {
    $exception = new RateLimitException('Too many requests', 429);

    expect($exception->getMessage())
        ->toBe('Too many requests');
    expect($exception->getCode())
        ->toBe(429);
});
