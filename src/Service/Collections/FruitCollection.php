<?php

namespace App\Service\Collections;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;

class FruitCollection extends BaseCollection
{
    public function __construct(FruitRepository $fruitRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($fruitRepository, $entityManager);
    }

    public function add($item): void
    {
        if (!$item instanceof Fruit) {
            throw new \InvalidArgumentException('Expected an instance of Fruit.');
        }

        parent::add($item);
    }

    public function remove($item): void
    {
        if (!$item instanceof Fruit) {
            throw new \InvalidArgumentException('Expected an instance of Fruit.');
        }

        parent::remove($item);
    }
}