<?php

declare(strict_types=1);

namespace LionTech\SDK;

/**
 * Internal helper for safe JSON decoding with type assertions.
 *
 * @phpstan-type ArrayType = array<string, mixed>
 */
final class Json
{
    /**
     * Decode JSON string to array.
     *
     * @return ArrayType
     */
    public static function decode(string $json): array
    {
        /** @var ArrayType */
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Assert that a value is an array.
     *
     * @return ArrayType
     */
    public static function assertArray(mixed $value): array
    {
        if (! is_array($value)) {
            throw new \InvalidArgumentException('Expected array, got ' . gettype($value));
        }

        /** @var ArrayType */
        return $value;
    }

    /**
     * Assert that an array contains only arrays.
     *
     * @return list<ArrayType>
     */
    public static function assertArrayOfArrays(mixed $value): array
    {
        $array = self::assertArray($value);

        return array_values(array_map(self::assertArray(...), $array));
    }

    /**
     * Get a string value from array or return default.
     *
     * @param ArrayType $array
     */
    public static function getString(array $array, string $key, string $default = ''): string
    {
        $value = $array[$key] ?? $default;

        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $default;
    }

    /**
     * Get an int value from array or return default.
     *
     * @param ArrayType $array
     */
    public static function getInt(array $array, string $key, int $default = 0): int
    {
        $value = $array[$key] ?? $default;

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    /**
     * Get a bool value from array or return default.
     *
     * @param ArrayType $array
     */
    public static function getBool(array $array, string $key, bool $default = false): bool
    {
        $value = $array[$key] ?? $default;

        return is_bool($value) ? $value : (bool) $value;
    }

    /**
     * Get a string|null value from array.
     *
     * @param ArrayType $array
     */
    public static function getNullableString(array $array, string $key): ?string
    {
        $value = $array[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return null;
    }

    /**
     * Get an array|null value from array.
     *
     * @param ArrayType $array
     * @return ArrayType|null
     */
    public static function getNullableArray(array $array, string $key): ?array
    {
        $value = $array[$key] ?? null;

        if (! is_array($value)) {
            return null;
        }

        /** @var ArrayType */
        return $value;
    }
}
