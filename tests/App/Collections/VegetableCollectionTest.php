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
        $vegetable->setId(1);

        // Mock the repository to return null before adding and the fruit after adding
        $this->vegetableRepositoryMock->expects($this->exactly(2))
            ->method('find')
            ->with(1)
            ->willReturnOnConsecutiveCalls(null, $vegetable);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($vegetable));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // Assert that the vegetable doesn't exist before adding
        $this->assertNull($this->vegetableCollection->findById(1));

        $this->vegetableCollection->add($vegetable);

        // Assert that the vegetable exists after adding
        $this->assertSame($vegetable, $this->vegetableCollection->findById(1));
    }

    // Edge case: Adding null
    public function testAddNull()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->vegetableCollection->add(null);
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
        $vegetable->setId(1);

        $this->vegetableRepositoryMock->expects($this->exactly(2))
            ->method('find')
            ->with(1)
            ->willReturnOnConsecutiveCalls($vegetable, null);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($vegetable));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // Assert that the vegetable exists before removal
        $this->assertSame($vegetable, $this->vegetableCollection->findById(1));

        $this->vegetableCollection->remove($vegetable);

        // Assert that the vegetable can't be found after removal
        $this->assertNull($this->vegetableCollection->findById(1));
    }

    public function testRemoveInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Vegetable.');

        $this->vegetableCollection->remove(new \stdClass());
    }
}
