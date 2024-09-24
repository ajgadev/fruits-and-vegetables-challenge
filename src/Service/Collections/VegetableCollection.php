<?php

namespace App\Service\Collections;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use Doctrine\ORM\EntityManagerInterface;

class VegetableCollection extends BaseCollection
{
    public function __construct(VegetableRepository $vegetableRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($vegetableRepository, $entityManager);
    }

    public function add($item): void
    {
        if (!$item instanceof Vegetable) {
            throw new \InvalidArgumentException('Expected an instance of Vegetable.');
        }

        parent::add($item);
    }

    public function remove($item): void
    {
        if (!$item instanceof Vegetable) {
            throw new \InvalidArgumentException('Expected an instance of Vegetable.');
        }

        parent::remove($item);
    }
}