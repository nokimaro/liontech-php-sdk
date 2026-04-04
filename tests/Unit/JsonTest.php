<?php

declare(strict_types=1);

use Nokimaro\LionTech\Json;

it('decodes valid JSON', function (): void {
    $result = Json::decode('{"key": "value"}');

    expect($result)
        ->toBe([
            'key' => 'value',
        ]);
});

it('throws on invalid JSON', function (): void {
    Json::decode('invalid json');
})->throws(JsonException::class);

it('asserts array', function (): void {
    $result = Json::assertArray([
        'key' => 'value',
    ]);

    expect($result)
        ->toBe([
            'key' => 'value',
        ]);
});

it('throws when asserting non-array', function (): void {
    Json::assertArray('not an array');
})->throws(InvalidArgumentException::class);

it('asserts array of arrays', function (): void {
    $result = Json::assertArrayOfArrays([
        [
            'key1' => 'value1',
        ],
        [
            'key2' => 'value2',
        ],
    ]);

    expect($result)
        ->toBe([
            [
                'key1' => 'value1',
            ],
            [
                'key2' => 'value2',
            ],
        ]);
});

it('converts int to string in getString', function (): void {
    $result = Json::getString([
        'count' => 42,
    ], 'count');

    expect($result)
        ->toBe('42');
});

it('converts float to string in getString', function (): void {
    $result = Json::getString([
        'price' => 19.99,
    ], 'price');

    expect($result)
        ->toBe('19.99');
});

it('returns default when key missing in getString', function (): void {
    $result = Json::getString([], 'missing', 'default');

    expect($result)
        ->toBe('default');
});

it('returns default when value is not string in getString', function (): void {
    $result = Json::getString([
        'key' => true,
    ], 'key', 'default');

    expect($result)
        ->toBe('default');
});

it('gets int from array', function (): void {
    $result = Json::getInt([
        'count' => 42,
    ], 'count');

    expect($result)
        ->toBe(42);
});

it('converts numeric string to int in getInt', function (): void {
    $result = Json::getInt([
        'count' => '42',
    ], 'count');

    expect($result)
        ->toBe(42);
});

it('returns default when key missing in getInt', function (): void {
    $result = Json::getInt([], 'missing', 0);

    expect($result)
        ->toBe(0);
});

it('returns default when value is not numeric in getInt', function (): void {
    $result = Json::getInt([
        'key' => 'not_numeric',
    ], 'key', 0);

    expect($result)
        ->toBe(0);
});

it('gets bool from array', function (): void {
    $result = Json::getBool([
        'active' => true,
    ], 'active');

    expect($result)
        ->toBeTrue();
});

it('casts non-bool to bool in getBool', function (): void {
    $result = Json::getBool([
        'active' => 1,
    ], 'active');

    expect($result)
        ->toBeTrue();
});

it('returns default when key missing in getBool', function (): void {
    $result = Json::getBool([], 'missing', false);

    expect($result)
        ->toBeFalse();
});

it('gets nullable string', function (): void {
    $result = Json::getNullableString([
        'name' => 'John',
    ], 'name');

    expect($result)
        ->toBe('John');
});

it('returns null when key missing in getNullableString', function (): void {
    $result = Json::getNullableString([], 'missing');

    expect($result)
        ->toBeNull();
});

it('converts int to string in getNullableString', function (): void {
    $result = Json::getNullableString([
        'count' => 42,
    ], 'count');

    expect($result)
        ->toBe('42');
});

it('converts float to string in getNullableString', function (): void {
    $result = Json::getNullableString([
        'price' => 19.99,
    ], 'price');

    expect($result)
        ->toBe('19.99');
});

it('returns null when value is not string in getNullableString', function (): void {
    $result = Json::getNullableString([
        'key' => true,
    ], 'key');

    expect($result)
        ->toBeNull();
});

it('gets nullable array', function (): void {
    $result = Json::getNullableArray([
        'items' => ['a', 'b'],
    ], 'items');

    expect($result)
        ->toBe(['a', 'b']);
});

it('returns null when key missing in getNullableArray', function (): void {
    $result = Json::getNullableArray([], 'missing');

    expect($result)
        ->toBeNull();
});

it('returns null when value is not array in getNullableArray', function (): void {
    $result = Json::getNullableArray([
        'key' => 'not_array',
    ], 'key');

    expect($result)
        ->toBeNull();
});
