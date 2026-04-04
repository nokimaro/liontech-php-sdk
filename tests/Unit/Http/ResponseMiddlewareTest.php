<?php

declare(strict_types=1);

use Nokimaro\LionTech\Http\ResponseMiddleware;
use Psr\Http\Message\ResponseInterface;

it('invokes middleware handler', function (): void {
    $response = Mockery::mock(ResponseInterface::class);
    $modifiedResponse = Mockery::mock(ResponseInterface::class);

    $middleware = new ResponseMiddleware(static fn ($resp) => $modifiedResponse);

    $result = $middleware($response);

    expect($result)
        ->toBe($modifiedResponse);
});

it('creates noop middleware', function (): void {
    $response = Mockery::mock(ResponseInterface::class);

    $middleware = ResponseMiddleware::noop();

    $result = $middleware($response);

    expect($result)
        ->toBe($response);
});
