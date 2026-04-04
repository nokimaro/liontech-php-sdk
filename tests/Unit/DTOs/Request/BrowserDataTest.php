<?php

declare(strict_types=1);

use LionTech\SDK\DTOs\Request\BrowserData;

it('serializes all fields when set', function (): void {
    $browserData = new BrowserData(
        acceptHeader: 'text/html',
        colorDepth: '24',
        javaEnabled: true,
        language: 'en-US',
        screenHeight: 1080,
        screenWidth: 1920,
        timezone: 'UTC',
        userAgent: 'Mozilla/5.0',
        windowHeight: 900,
        windowWidth: 1600,
    );

    $json = $browserData->jsonSerialize();

    expect($json)->toBe([
        'acceptHeader' => 'text/html',
        'colorDepth' => '24',
        'javaEnabled' => true,
        'language' => 'en-US',
        'screenHeight' => 1080,
        'screenWidth' => 1920,
        'timezone' => 'UTC',
        'userAgent' => 'Mozilla/5.0',
        'windowHeight' => 900,
        'windowWidth' => 1600,
    ]);
});

it('returns empty array when no fields set', function (): void {
    $browserData = new BrowserData();

    $json = $browserData->jsonSerialize();

    expect($json)->toBe([]);
});

it('serializes partial data', function (): void {
    $browserData = new BrowserData(
        userAgent: 'Mozilla/5.0',
        language: 'en',
    );

    $json = $browserData->jsonSerialize();

    expect($json)->toHaveKeys(['userAgent', 'language']);
    expect($json)->not->toHaveKey('acceptHeader');
});

it('is immutable', function (): void {
    $browserData = new BrowserData();
    $reflection = new ReflectionClass($browserData);

    expect($reflection->isReadOnly())->toBeTrue();
});
