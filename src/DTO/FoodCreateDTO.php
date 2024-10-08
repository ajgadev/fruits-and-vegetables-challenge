<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FoodCreateDTO
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Type('string')]
    public $name;

    #[Assert\NotBlank(message: 'Quantity is required')]
    #[Assert\Type('numeric')]
    public $quantity;

    public function __construct(string $name, float $quantity)
    {
        $this->name = $name;
        $this->quantity = $quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }
}