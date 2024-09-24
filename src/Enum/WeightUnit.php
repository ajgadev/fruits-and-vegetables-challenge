<?php

namespace App\Enum;

class WeightUnit
{
    const GRAM = 'g';
    const KILOGRAM = 'kg';
    
    public const UNITS = [
        self::GRAM,
        self::KILOGRAM,
    ];

    // Grams is the default unit
    private static array $conversionFactors = [
        self::GRAM => 1,
        self::KILOGRAM => 1000,
    ];

    public static function toGrams(int $quantity, string $unit): float
    {
        if (!isset(self::$conversionFactors[$unit])) {
            throw new \InvalidArgumentException("Unsupported unit provided: $unit");
        }
        return $quantity * self::$conversionFactors[$unit];
    }

    public static function fromGrams(float $grams, string $unit): float
    {
        if (!isset(self::$conversionFactors[$unit])) {
            throw new \InvalidArgumentException("Unsupported unit provided: $unit");
        }
        return $grams / self::$conversionFactors[$unit];
    }

    public static function convert(int $quantity, string $fromUnit, string $toUnit): float
    {
        $grams = self::toGrams($quantity, $fromUnit);
        return self::fromGrams($grams, $toUnit);
    }

    public static function getAll(): array
    {
        return self::UNITS;
    }
}
