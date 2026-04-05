<?php

declare(strict_types=1);

use Nokimaro\LionTech\Exceptions\Auth\AuthenticationException;
use Nokimaro\LionTech\Exceptions\Auth\TokenExpiredException;
use Nokimaro\LionTech\Exceptions\Business\ConflictException;
use Nokimaro\LionTech\Exceptions\RateLimitException;
use Nokimaro\LionTech\Exceptions\ResourceNotFoundException;
use Nokimaro\LionTech\Exceptions\Transport\TransportException;
use Nokimaro\LionTech\Exceptions\Validation\ValidationException;
use Nokimaro\LionTech\Http\ApiExceptionMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

function createErrorResponse(int $statusCode, ?string $body = null): ResponseInterface
{
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $stream->shouldReceive('__toString')
        ->andReturn($body ?? '{}');
    $response->shouldReceive('getStatusCode')
        ->andReturn($statusCode);
    $response->shouldReceive('getBody')
        ->andReturn($stream);

    return $response;
}

it('throws AuthenticationException for 401', function (): void {
    $response = createErrorResponse(401, json_encode([
        'code' => 401,
        'description' => 'Unauthorized',
    ]));

    ApiExceptionMapper::map($response);
})->throws(AuthenticationException::class);

it('throws TokenExpiredException for 403 with code 504', function (): void {
    $response = createErrorResponse(403, json_encode([
        'code' => 504,
        'description' => 'Token expired',
    ]));

    ApiExceptionMapper::map($response);
})->throws(TokenExpiredException::class);

it('throws AuthenticationException for 403 without code 504', function (): void {
    $response = createErrorResponse(403, json_encode([
        'code' => 403,
        'description' => 'Forbidden',
    ]));

    ApiExceptionMapper::map($response);
})->throws(AuthenticationException::class);

it('throws ValidationException for 400', function (): void {
    $response = createErrorResponse(400, json_encode([
        'code' => 400,
        'description' => 'Bad Request',
    ]));

    ApiExceptionMapper::map($response);
})->throws(ValidationException::class);

it('throws ValidationException for 422', function (): void {
    $response = createErrorResponse(422, json_encode([
        'code' => 422,
        'description' => 'Unprocessable Entity',
    ]));

    ApiExceptionMapper::map($response);
})->throws(ValidationException::class);

it('throws ResourceNotFoundException for 404', function (): void {
    $response = createErrorResponse(404, json_encode([
        'code' => 404,
        'description' => 'Not Found',
    ]));

    ApiExceptionMapper::map($response);
})->throws(ResourceNotFoundException::class);

it('throws ConflictException for 409', function (): void {
    $response = createErrorResponse(409, json_encode([
        'code' => 409,
        'description' => 'Conflict',
    ]));

    ApiExceptionMapper::map($response);
})->throws(ConflictException::class);

it('throws RateLimitException for 429', function (): void {
    $response = createErrorResponse(429, json_encode([
        'code' => 429,
        'description' => 'Rate Limited',
    ]));

    ApiExceptionMapper::map($response);
})->throws(RateLimitException::class);

it('throws TransportException for 500', function (): void {
    $response = createErrorResponse(500, '{}');

    ApiExceptionMapper::map($response);
})->throws(TransportException::class);

it('throws TransportException for 502', function (): void {
    $response = createErrorResponse(502, '{}');

    ApiExceptionMapper::map($response);
})->throws(TransportException::class);

it('throws TransportException for 503', function (): void {
    $response = createErrorResponse(503, '{}');

    ApiExceptionMapper::map($response);
})->throws(TransportException::class);

it('throws TransportException for unknown error code', function (): void {
    $response = createErrorResponse(418, '{}');

    ApiExceptionMapper::map($response);
})->throws(TransportException::class);

it('uses error description from API response', function (): void {
    $response = createErrorResponse(400, json_encode([
        'code' => 400,
        'description' => 'Custom error message',
    ]));

    try {
        ApiExceptionMapper::map($response);
    } catch (ValidationException $e) {
        expect($e->getMessage())
            ->toBe('Custom error message');
    }
});

it('uses default message when body is empty json object', function (): void {
    $response = createErrorResponse(400, '{}');

    try {
        ApiExceptionMapper::map($response);
    } catch (ValidationException $e) {
        expect($e->getMessage())
            ->toBe('Bad Request');
    }
});

it('uses default message when body is not valid json', function (): void {
    $response = createErrorResponse(500, 'not json');

    ApiExceptionMapper::map($response);
})->throws(\Nokimaro\LionTech\Exceptions\Transport\TransportException::class, 'Internal Server Error');

it('throws ValidationException for 400 without body', function (): void {
    $response = createErrorResponse(400);

    ApiExceptionMapper::map($response);
})->throws(ValidationException::class, 'Bad Request');

it('throws AuthenticationException for 401 without body', function (): void {
    $response = createErrorResponse(401);

    ApiExceptionMapper::map($response);
})->throws(AuthenticationException::class, 'Unauthorized');

it('throws AuthenticationException for 403 without body', function (): void {
    $response = createErrorResponse(403);

    ApiExceptionMapper::map($response);
})->throws(AuthenticationException::class, 'Forbidden');

it('throws ResourceNotFoundException for 404 without body', function (): void {
    $response = createErrorResponse(404);

    ApiExceptionMapper::map($response);
})->throws(ResourceNotFoundException::class, 'Not Found');

it('throws ConflictException for 409 without body', function (): void {
    $response = createErrorResponse(409);

    ApiExceptionMapper::map($response);
})->throws(ConflictException::class, 'Conflict');

it('throws ValidationException for 422 without body', function (): void {
    $response = createErrorResponse(422);

    ApiExceptionMapper::map($response);
})->throws(ValidationException::class, 'Unprocessable Entity');

it('throws RateLimitException for 429 without body', function (): void {
    $response = createErrorResponse(429);

    ApiExceptionMapper::map($response);
})->throws(RateLimitException::class, 'Too Many Requests');

it('throws TransportException for 500 without body', function (): void {
    $response = createErrorResponse(500);

    ApiExceptionMapper::map($response);
})->throws(TransportException::class, 'Internal Server Error');

it('throws TransportException for 502 without body', function (): void {
    $response = createErrorResponse(502);

    ApiExceptionMapper::map($response);
})->throws(TransportException::class, 'Bad Gateway');

it('throws TransportException for 503 without body', function (): void {
    $response = createErrorResponse(503);

    ApiExceptionMapper::map($response);
})->throws(TransportException::class, 'Service Unavailable');

it('throws TransportException for 501 status code', function (): void {
    $response = createErrorResponse(501);
    ApiExceptionMapper::map($response);
})->throws(TransportException::class, 'HTTP Error 501');

it('throws TransportException for 418 status code', function (): void {
    $response = createErrorResponse(418);
    ApiExceptionMapper::map($response);
})->throws(TransportException::class, 'HTTP Error 418');

it('preserves error description from ApiErrorResponse', function (): void {
    $response = createErrorResponse(400, json_encode([
        'code' => 513,
        'description' => 'Custom error message',
    ]));

    try {
        ApiExceptionMapper::map($response);
    } catch (ValidationException $e) {
        expect($e->getMessage())
            ->toBe('Custom error message');
    }
});

it('throws ValidationException with preserved error details', function (): void {
    $response = createErrorResponse(400, json_encode([
        'code' => 513,
        'description' => 'Currency mismatch',
        'details' => [[
            'field' => 'currency',
            'issue' => 'invalid',
        ]],
    ]));

    try {
        ApiExceptionMapper::map($response);
    } catch (ValidationException $e) {
        expect($e->getErrors())
            ->toHaveKey('code', 513);
        expect($e->getErrors())
            ->toHaveKey('details');
    }
});
