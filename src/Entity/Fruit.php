<?php

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FruitRepository::class)]
#[ORM\Table(name: 'fruits')]
class Fruit extends Food {
    // Any additional properties or methods specifically for Fruit can be added here
}
