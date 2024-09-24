<?php

namespace App\Service\Collections;

interface CollectionInterface {
    public function add($item): void;
    public function remove($item): void;
    public function list(): array;
}