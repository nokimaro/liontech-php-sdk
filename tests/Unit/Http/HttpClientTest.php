<?php

declare(strict_types=1);

use LionTech\SDK\Http\HttpClient;
use LionTech\SDK\Http\ResponseMiddleware;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

function createHttpClientMocks(): array
{
    $httpClient = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $streamFactory->shouldReceive('createStream')
        ->andReturn($stream);
    $request->shouldReceive('withBody')
        ->andReturnSelf();
    $httpClient->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $stream->shouldReceive('__toString')
        ->andReturn('');

    return [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response];
}

it('creates with default factories', function (): void {
    $client = new HttpClient('https://api.example.com');

    expect($client)
        ->toBeInstanceOf(HttpClient::class);
});

it('creates with custom dependencies', function (): void {
    $httpClient = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);

    expect($client)
        ->toBeInstanceOf(HttpClient::class);
});

it('sets and gets access token', function (): void {
    $client = new HttpClient('https://api.example.com');

    expect($client->getAccessToken())
        ->toBeNull();

    $client->setAccessToken('token_123');

    expect($client->getAccessToken())
        ->toBe('token_123');
});

it('trims trailing slash from base url', function (): void {
    $client = new HttpClient('https://api.example.com/');

    $result = $client->getAccessToken();
    expect($result)
        ->toBeNull();
});

it('performs GET request', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);
    $result = $client->get('/test', [
        'key' => 'value',
    ]);

    expect($result)
        ->toBe($response);
});

it('performs POST request with null data', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);
    $result = $client->post('/test');

    expect($result)
        ->toBe($response);
});

it('performs POST request with JsonSerializable data', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);

    $data = new class() implements JsonSerializable {
        public function jsonSerialize(): array
        {
            return [
                'key' => 'value',
            ];
        }
    };

    $result = $client->post('/test', $data);

    expect($result)
        ->toBe($response);
});

it('performs POST request with array data', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);
    $result = $client->post('/test', [
        'key' => 'value',
    ]);

    expect($result)
        ->toBe($response);
});

it('performs PUT request', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);
    $result = $client->put('/test', [
        'key' => 'value',
    ]);

    expect($result)
        ->toBe($response);
});

it('performs DELETE request', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);
    $result = $client->delete('/test', [
        'key' => 'value',
    ]);

    expect($result)
        ->toBe($response);
});

it('applies response middleware', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();
    $modifiedResponse = Mockery::mock(ResponseInterface::class);
    $modifiedResponse->shouldReceive('getStatusCode')
        ->andReturn(200);

    $middleware = new ResponseMiddleware(static fn () => $modifiedResponse);

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);
    $client->setResponseMiddleware($middleware);
    $result = $client->get('/test');

    expect($result)
        ->toBe($modifiedResponse);
});

it('adds authorization header when token is set', function (): void {
    [$httpClient, $requestFactory, $streamFactory, $request, $stream, $response] = createHttpClientMocks();

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);
    $client->setAccessToken('token_123');
    $result = $client->get('/test');

    expect($result)
        ->toBe($response);
});

it('throws exception on HTTP error response', function (): void {
    $httpClient = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $request = Mockery::mock(RequestInterface::class);
    $stream = Mockery::mock(StreamInterface::class);
    $response = Mockery::mock(ResponseInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $httpClient->shouldReceive('sendRequest')
        ->with($request)
        ->andReturn($response);
    $response->shouldReceive('getStatusCode')
        ->andReturn(400);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $stream->shouldReceive('__toString')
        ->andReturn('{}');

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);

    $client->get('/test');
})->throws(\LionTech\SDK\Exceptions\Validation\ValidationException::class);

it('throws transport exception on HTTP client failure', function (): void {
    $httpClient = Mockery::mock(ClientInterface::class);
    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $request = Mockery::mock(RequestInterface::class);

    $requestFactory->shouldReceive('createRequest')
        ->andReturn($request);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $httpClient->shouldReceive('sendRequest')
        ->andThrow(new \Exception('Connection refused'));

    $client = new HttpClient('https://api.example.com', $httpClient, $requestFactory, $streamFactory);

    $client->get('/test');
})->throws(\LionTech\SDK\Exceptions\Transport\TransportException::class, 'HTTP request failed: Connection refused');

it('trims trailing slash with multiple slashes', function (): void {
    $client = new HttpClient('https://api.example.com///');
    $reflection = new ReflectionClass($client);
    $property = $reflection->getProperty('baseUrl');
    expect($property->getValue($client))
        ->toBe('https://api.example.com');
});

it('performs POST request without body', function (): void {
    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->andReturn('{"status":"ok"}');

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $request = Mockery::mock(RequestInterface::class);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();
    $request->shouldReceive('withBody')
        ->andReturnSelf();

    $clientMock = Mockery::mock(ClientInterface::class);
    $clientMock->shouldReceive('sendRequest')
        ->andReturn($response);

    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $requestFactory->shouldReceive('createRequest')
        ->andReturn($request);

    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->andReturn($stream);

    $httpClient = new HttpClient('https://api.example.com', $clientMock, $requestFactory, $streamFactory);
    $result = $httpClient->post('/test');

    expect($result)
        ->toBe($response);
});

it('applies accept header on all requests', function (): void {
    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->andReturn('{}');

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $request = Mockery::mock(RequestInterface::class);
    $request->shouldReceive('withHeader')
        ->with('Accept', 'application/json')
        ->andReturnSelf();
    $request->shouldReceive('withHeader')
        ->with('Authorization', Mockery::type('string'))
        ->andReturnSelf();
    $request->shouldReceive('withBody')
        ->andReturnSelf();

    $clientMock = Mockery::mock(ClientInterface::class);
    $clientMock->shouldReceive('sendRequest')
        ->andReturn($response);

    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $requestFactory->shouldReceive('createRequest')
        ->andReturn($request);

    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->andReturn($stream);

    $httpClient = new HttpClient('https://api.example.com', $clientMock, $requestFactory, $streamFactory);
    $httpClient->setAccessToken('test_token');
    $httpClient->get('/test');
});

it('performs DELETE request with query parameters', function (): void {
    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->andReturn('{}');

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $request = Mockery::mock(RequestInterface::class);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();

    $clientMock = Mockery::mock(ClientInterface::class);
    $clientMock->shouldReceive('sendRequest')
        ->andReturn($response);

    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $requestFactory->shouldReceive('createRequest')
        ->with('DELETE', 'https://api.example.com/test?key=value')
        ->andReturn($request);

    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $httpClient = new HttpClient('https://api.example.com', $clientMock, $requestFactory, $streamFactory);
    $result = $httpClient->delete('/test', [
        'key' => 'value',
    ]);

    expect($result)
        ->toBe($response);
});

it('builds URL without query parameters', function (): void {
    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->andReturn('{}');

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getBody')
        ->andReturn($stream);
    $response->shouldReceive('getStatusCode')
        ->andReturn(200);

    $request = Mockery::mock(RequestInterface::class);
    $request->shouldReceive('withHeader')
        ->andReturnSelf();

    $clientMock = Mockery::mock(ClientInterface::class);
    $clientMock->shouldReceive('sendRequest')
        ->andReturn($response);

    $requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $requestFactory->shouldReceive('createRequest')
        ->with('GET', 'https://api.example.com/test')
        ->andReturn($request);

    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $httpClient = new HttpClient('https://api.example.com', $clientMock, $requestFactory, $streamFactory);
    $result = $httpClient->get('/test');

    expect($result)
        ->toBe($response);
});
