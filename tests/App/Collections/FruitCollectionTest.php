<?php

namespace App\Tests\Service\Collections;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Service\Collections\FruitCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FruitCollectionTest extends TestCase
{
    /** @var FruitRepository&MockObject */
    private $fruitRepositoryMock;
    /** @var EntityManagerInterface&MockObject */
    private $entityManagerMock;
    private $fruitCollection;

    protected function setUp(): void
    {
        $this->fruitRepositoryMock = $this->createMock(FruitRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->fruitCollection = new FruitCollection($this->fruitRepositoryMock, $this->entityManagerMock);
    }

    public function testAddFruit()
    {
        $fruit = new Fruit();
        
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($fruit));
        
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->fruitCollection->add($fruit);
    }

    public function testAddInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Fruit.');

        $this->fruitCollection->add(new \stdClass());
    }

    public function testRemoveFruit()
    {
        $fruit = new Fruit();
        
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($fruit));
        
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->fruitCollection->remove($fruit);
    }

    public function testRemoveInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Fruit.');

        $this->fruitCollection->remove(new \stdClass());
    }
}