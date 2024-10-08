<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\WeightUnit;

class FoodPaginationDTO
{
    public function __construct(
        #[Assert\Type('string')]
        public string|null $name,
        #[Assert\GreaterThan(0)]
        #[Assert\Type('numeric')]
        public int $page,
        #[Assert\GreaterThan(0)]
        #[Assert\Type('numeric')]
        public int $limit,
        #[Assert\Choice(choices:WeightUnit::UNITS, message: 'Unsupported unit provided, please use one of the following: {{ choices }}')]
        #[Assert\Type('string')]
        public string $unit
    ) {
    }

    public function getName(): string|null
    {
        return $this->name;
    }
    public function getPage(): int
    {
        return $this->page;
    }
    public function getLimit(): int
    {
        return $this->limit;
    }
    public function getUnit(): string
    {
        return $this->unit;
    }
}