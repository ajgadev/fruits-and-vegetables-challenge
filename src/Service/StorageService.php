<?php

namespace App\Service;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Enum\FoodType;
use App\Enum\WeightUnit;
use App\Service\Collections\FruitCollection;
use App\Service\Collections\VegetableCollection;

use App\Exception\InvalidItemDataException;
use App\Exception\UnsupportedFoodTypeException;
use App\Exception\UnsupportedWeightUnitException;
use App\Exception\JsonDecodeException;

class StorageService
{
    protected string $request = '';
    protected FruitCollection $fruitCollection;
    protected VegetableCollection $vegetableCollection;

    public function __construct(
        FruitCollection $fruitCollection,
        VegetableCollection $vegetableCollection,
        string $request = ''
    ) {
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

    private function validateItem(array $item): void
    {
        if (!isset($item['type'], $item['name'], $item['quantity'])) {
            throw new InvalidItemDataException("Invalid item data");
        }

        if (!in_array($item['type'], FoodType::getAll(), true)) {
            throw new UnsupportedFoodTypeException("Unsupported food type: {$item['type']}");
        }

        $weightUnit = $item['unit'] ?? WeightUnit::GRAM;
        if (!in_array($weightUnit, WeightUnit::getAll(), true)) {
            throw new UnsupportedWeightUnitException("Unsupported weight unit: $weightUnit");
        }
    }

    private function createAndStoreFood(array $item): void
    {
        $type = $item['type'];
        $name = $item['name'];
        $quantity = $item['quantity'];
        $weightUnit = $item['unit'] ?? WeightUnit::GRAM;

        $quantityInGrams = WeightUnit::toGrams($quantity, $weightUnit);

        if ($type === FoodType::FRUIT) {
            $food = new Fruit();
            $collection = $this->fruitCollection;
        } else {
            $food = new Vegetable();
            $collection = $this->vegetableCollection;
        }

        $food->setName($name);
        $food->setQuantity($quantityInGrams);
        $collection->add($food);
    }

    public function processRequest(): void
    {
        $data = json_decode($this->request, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodeException("Failed to decode JSON: " . json_last_error_msg());
        }

        foreach ($data as $item) {
            $this->validateItem($item);
            $this->createAndStoreFood($item);
        }
    }
}
