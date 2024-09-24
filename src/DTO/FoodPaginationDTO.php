<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\WeightUnit;

class FoodPaginationDTO
{
    #[Assert\Type('string')]
    public $name;

    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    public $page;

    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    public $limit;

    #[Assert\Choice(choices:WeightUnit::UNITS, message: 'Unsupported unit provided, please use one of the following: {{ choices }}')]
    #[Assert\Type('string')]
    public $unit;
}