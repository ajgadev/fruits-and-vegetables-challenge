<?php

namespace App\Entity\Traits;

trait WeightUnitTrait
{
    /**
     * @var string|null Holds the weight unit (transient property, not persisted)
     */
    private ?string $weightUnit = 'g';

    public function getWeightUnit(): ?string
    {
        return $this->weightUnit;
    }

    public function setWeightUnit(?string $weightUnit): self
    {
        $this->weightUnit = $weightUnit;
        return $this;
    }
}