<?php

namespace App\Service;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Enum\FoodType;
use App\Enum\WeightUnit;
use App\Service\Collections\FruitCollection;
use App\Service\Collections\VegetableCollection;

class StorageService
{
    protected string $request = '';
    protected FruitCollection $fruitCollection;
    protected VegetableCollection $vegetableCollection;

    public function __construct(
        FruitCollection $fruitCollection,
        VegetableCollection $vegetableCollection,
        string $request = ''
    )
    {
        $this->fruitCollection = $fruitCollection;
        $this->vegetableCollection = $vegetableCollection;
        $this->request = $request;
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function setRequest(string $request): void
    {
        $this->request = $request;
    }

    public function processRequest(): void
    {
        $data = json_decode($this->request, true);

        foreach ($data as $item) {
            if (!isset($item['type'], $item['name'], $item['quantity'])) {
                throw new \InvalidArgumentException("Invalid item data");
            }

            $type = $item['type'];
            $name = $item['name'];
            $quantity = $item['quantity'];
            $weightUnit = $item['unit'] ?? WeightUnit::GRAM;

            if (!in_array($type, FoodType::getAll(), true)) {
                throw new \InvalidArgumentException("Unsupported food type: $type");
            }
            
            if (!in_array($weightUnit, WeightUnit::getAll(), true)) {
                throw new \InvalidArgumentException("Unsupported weight unit: $weightUnit");
            }

            $quantityInGrams = WeightUnit::toGrams($quantity, $weightUnit);

            if ($type === FoodType::FRUIT) {
                $fruit = new Fruit();
                $fruit->setName($name);
                $fruit->setQuantity($quantityInGrams);

                $this->fruitCollection->add($fruit);
            }
            
            if ($type === FoodType::VEGETABLE) {
                $vegetable = new Vegetable();
                $vegetable->setName($name);
                $vegetable->setQuantity($quantityInGrams);
                $this->vegetableCollection->add($vegetable);
            }
        }
    }
}
