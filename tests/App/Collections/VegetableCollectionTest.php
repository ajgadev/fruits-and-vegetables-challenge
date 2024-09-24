<?php

namespace App\Tests\Service\Collections;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use App\Service\Collections\VegetableCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class VegetableCollectionTest extends TestCase
{
    /** @var VegetableRepository&MockObject */
    private $vegetableRepositoryMock;
    /** @var EntityManagerInterface&MockObject */
    private $entityManagerMock;
    private $vegetableCollection;

    protected function setUp(): void
    {
        $this->vegetableRepositoryMock = $this->createMock(VegetableRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->vegetableCollection = new VegetableCollection($this->vegetableRepositoryMock, $this->entityManagerMock);
    }

    public function testAddVegetable()
    {
        $vegetable = new Vegetable();
        
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($vegetable));
        
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->vegetableCollection->add($vegetable);
    }

    public function testAddInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Vegetable.');

        $this->vegetableCollection->add(new \stdClass());
    }

    public function testRemoveVegetable()
    {
        $vegetable = new Vegetable();
        
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($vegetable));
        
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->vegetableCollection->remove($vegetable);
    }

    public function testRemoveInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Vegetable.');

        $this->vegetableCollection->remove(new \stdClass());
    }
}