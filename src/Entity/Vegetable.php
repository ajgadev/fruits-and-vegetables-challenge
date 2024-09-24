<?php

namespace App\Entity;

use App\Repository\VegetableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VegetableRepository::class)]
#[ORM\Table(name: 'vegetables')] // Optional, based on your naming requirements
class Vegetable extends Food {
    // Additional properties or methods specifically for Vegetable can be added here
}
