<?php

namespace App\Service;

class FoodService {

    public function createFood($collection, $name, $quantity) {
        $food = new $collection();
        $food->setName($name);
        $food->setQuantity($quantity);
        return $food;
    }
}