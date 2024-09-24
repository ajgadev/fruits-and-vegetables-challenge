<?php

namespace App\Enum;

class FoodType
{
    public const VEGETABLE = 'vegetable';
    public const FRUIT = 'fruit';

    public static function getAll(): array
    {
        return [self::VEGETABLE, self::FRUIT];
    }
}