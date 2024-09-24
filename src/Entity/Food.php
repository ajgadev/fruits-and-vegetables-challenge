<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\WeightUnitTrait;

/** 
 * @ORM\MappedSuperclass 
 */
#[ORM\MappedSuperclass]
abstract class Food 
{
    use WeightUnitTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'float')]
    private $quantity;

    // Getters and Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getQuantity(): ?float {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }
}
