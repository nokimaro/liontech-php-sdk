<?php

declare(strict_types=1);

namespace LionTech\SDK\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use LionTech\SDK\Exceptions\ApiExceptionMapper;
use LionTech\SDK\Exceptions\Auth\TokenExpiredException;
use LionTech\SDK\Exceptions\Transport\TransportException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class HttpClient
{
    private readonly ClientInterface $client;
    private readonly RequestFactoryInterface $requestFactory;
    private readonly StreamFactoryInterface $streamFactory;
    private readonly string $baseUrl;
    private ?string $accessToken = null;
    private ?ResponseMiddleware $responseMiddleware = null;

    public function __construct(
        string $baseUrl,
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->client = $client ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setResponseMiddleware(?ResponseMiddleware $middleware): self
    {
        $this->responseMiddleware = $middleware;

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     * @throws TransportException
     * @throws TokenExpiredException
     */
    public function get(string $path, array $data = []): ResponseInterface
    {
        $url = $this->buildUrl($path, $data);
        $request = $this->requestFactory->createRequest('GET', $url);

        return $this->send($request);
    }

    /**
     * @throws TransportException
     * @throws TokenExpiredException
     */
    public function post(string $path, mixed $data = null): ResponseInterface
    {
        $request = $this->buildJsonRequest('POST', $path, $data);

        return $this->send($request);
    }

    /**
     * @throws TransportException
     * @throws TokenExpiredException
     */
    public function put(string $path, mixed $data = null): ResponseInterface
    {
        $request = $this->buildJsonRequest('PUT', $path, $data);

        return $this->send($request);
    }

    /**
     * @throws TransportException
     * @throws TokenExpiredException
     */
    public function delete(string $path, array $data = []): ResponseInterface
    {
        $url = $this->buildUrl($path, $data);
        $request = $this->requestFactory->createRequest('DELETE', $url);

        return $this->send($this->applyHeaders($request));
    }

    /**
     * @throws TransportException
     * @throws TokenExpiredException
     */
    private function send(RequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->client->sendRequest($request);
        } catch (\Exception $e) {
            throw new TransportException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }

        // Apply response middleware if set
        if ($this->responseMiddleware !== null) {
            $response = ($this->responseMiddleware)($response);
        }

        // Map error responses to exceptions
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            ApiExceptionMapper::map($response);
        }

        return $response;
    }

    private function buildJsonRequest(string $method, string $path, mixed $data = null): RequestInterface
    {
        $url = $this->buildUrl($path);
        $request = $this->requestFactory->createRequest($method, $url);
        $request = $this->applyHeaders($request, ['Content-Type' => 'application/json']);

        if ($data !== null) {
            $json = $data instanceof \JsonSerializable ? json_encode($data, JSON_THROW_ON_ERROR) : json_encode($data, JSON_THROW_ON_ERROR);
            $request = $request->withBody($this->streamFactory->createStream($json));
        }

        return $request;
    }

    private function applyHeaders(RequestInterface $request, array $additionalHeaders = []): RequestInterface
    {
        $headers = array_merge([
            'Accept' => 'application/json',
        ], $additionalHeaders);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($this->accessToken !== null) {
            $request = $request->withHeader('Authorization', 'Bearer ' . $this->accessToken);
        }

        return $request;
    }

    /**
     * @param array<string, mixed> $query
     */
    private function buildUrl(string $path, array $query = []): string
    {
        $url = $this->baseUrl . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}
