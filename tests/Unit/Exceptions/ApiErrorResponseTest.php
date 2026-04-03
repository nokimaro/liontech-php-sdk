<?php

declare(strict_types=1);

use LionTech\SDK\Exceptions\ApiErrorResponse;

it('creates API error response', function (): void {
    $error = new ApiErrorResponse(
        code: 504,
        description: 'Token expired',
        traceId: 'trace_123',
        details: ['detail1', 'detail2'],
    );

    expect($error->code)
        ->toBe(504);
    expect($error->description)
        ->toBe('Token expired');
    expect($error->traceId)
        ->toBe('trace_123');
    expect($error->details)
        ->toBe(['detail1', 'detail2']);
});

it('creates API error response from array', function (): void {
    $data = [
        'code' => 513,
        'description' => 'Currency mismatch',
        'traceId' => 'trace_456',
        'details' => [],
    ];

    $error = ApiErrorResponse::fromArray($data);

    expect($error->code)
        ->toBe(513);
    expect($error->description)
        ->toBe('Currency mismatch');
    expect($error->traceId)
        ->toBe('trace_456');
});

it('handles missing optional fields', function (): void {
    $data = [
        'code' => 500,
        'description' => 'Internal error',
    ];

    $error = ApiErrorResponse::fromArray($data);

    expect($error->code)
        ->toBe(500);
    expect($error->description)
        ->toBe('Internal error');
    expect($error->traceId)
        ->toBeNull();
    expect($error->details)
        ->toBe([]);
});
