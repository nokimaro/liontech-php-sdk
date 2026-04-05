<?php

declare(strict_types=1);

namespace Nokimaro\LionTech\Http;

use Nokimaro\LionTech\Exceptions\Auth\AuthenticationException;
use Nokimaro\LionTech\Exceptions\Auth\TokenExpiredException;
use Nokimaro\LionTech\Exceptions\Business\ConflictException;
use Nokimaro\LionTech\Exceptions\RateLimitException;
use Nokimaro\LionTech\Exceptions\ResourceNotFoundException;
use Nokimaro\LionTech\Exceptions\Transport\TransportException;
use Nokimaro\LionTech\Exceptions\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;

class ApiExceptionMapper
{
    /**
     * Map HTTP response to appropriate exception.
     */
    public static function map(ResponseInterface $response): never
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        /** @var mixed $rawErrorData */
        $rawErrorData = json_decode($body, true, 512);
        /** @var array<string, mixed> $errorData */
        $errorData = is_array($rawErrorData) ? $rawErrorData : [];

        // API responses are wrapped in {type, object, error} envelope — extract inner error
        /** @var array<string, mixed> $innerError */
        $innerError = is_array($errorData['error'] ?? null) ? $errorData['error'] : $errorData;

        $apiError = empty($innerError) ? null : ApiErrorResponse::fromArray($innerError);

        $message = $apiError instanceof \Nokimaro\LionTech\Http\ApiErrorResponse ? $apiError->description : match ($statusCode) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            409 => 'Conflict',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            // @pest-mutate-ignore -- Status code is intentionally concatenated
            default => 'HTTP Error ' . $statusCode,
        };

        match (true) {
            // Authentication errors
            $statusCode === 401 => throw new AuthenticationException($message, $statusCode),
            $statusCode === 403 && $apiError?->code === 504 => throw new TokenExpiredException($message, $statusCode),
            $statusCode === 403 => throw new AuthenticationException($message, $statusCode),

            // Validation errors
            $statusCode === 400 => throw new ValidationException($message, $statusCode, $errorData),
            $statusCode === 422 => throw new ValidationException($message, $statusCode, $errorData),

            // Not found
            $statusCode === 404 => throw new ResourceNotFoundException($message, $statusCode),

            // Conflict
            $statusCode === 409 => throw new ConflictException($message, $statusCode),

            // Rate limit
            $statusCode === 429 => throw new RateLimitException($message, $statusCode),

            // @pest-mutate-ignore -- >= 500 covers all server errors
            // Server errors
            $statusCode >= 500 => throw new TransportException($message, $statusCode),

            // Default
            default => throw new TransportException($message, $statusCode),
        };
    }
}
