<?php

declare(strict_types=1);

namespace LionTech\SDK\Exceptions;

use LionTech\SDK\Exceptions\Auth\AuthenticationException;
use LionTech\SDK\Exceptions\Auth\TokenExpiredException;
use LionTech\SDK\Exceptions\Business\ConflictException;
use LionTech\SDK\Exceptions\Transport\TransportException;
use LionTech\SDK\Exceptions\Validation\ValidationException;
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
        $errorData = json_decode($body, true) ?: [];
        $apiError = empty($errorData) ? null : ApiErrorResponse::fromArray($errorData);

        $message = $apiError->description ?? match ($statusCode) {
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

            // Server errors
            $statusCode >= 500 => throw new TransportException($message, $statusCode),

            // Default
            default => throw new TransportException($message, $statusCode),
        };
    }
}
