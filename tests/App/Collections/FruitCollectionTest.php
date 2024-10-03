<?php

namespace App\Tests\Service\Collections;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Service\Collections\FruitCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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

        $queryBuilderMock = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the creation of the query builder
        // $this->fruitRepositoryMock->method('createQueryBuilder')
        //     ->willReturn($queryBuilderMock);

        // // Mock the method chain on the QueryBuilder
        // $queryMock = $this->createMock(\Doctrine\ORM\AbstractQuery::class);

        // $queryMock->expects($this->any())
        //     ->method('getResult')
        //     ->willReturn([]); // Ensure this returns an array

        // $queryBuilderMock->method('getQuery')
        //     ->willReturn($queryMock);
    }

    public function testAddFruit()
    {
        $fruit = new Fruit();
        $fruit->setId(1);

        // Mock the repository to return null before adding and the fruit after adding
        $this->fruitRepositoryMock->expects($this->exactly(2))
            ->method('find')
            ->with(1)
            ->willReturnOnConsecutiveCalls(null, $fruit);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($fruit));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // Assert that the fruit doesn't exist before adding
        $this->assertNull($this->fruitCollection->findById(1));

        $this->fruitCollection->add($fruit);

        // Assert that the fruit exists after adding
        $this->assertSame($fruit, $this->fruitCollection->findById(1));
    }

    public function testAddInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Fruit.');

        $this->fruitCollection->add(new \stdClass());
    }

    // Edge case: Adding null
    public function testAddNull()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fruitCollection->add(null);
    }

    public function testRemoveFruit()
    {
        $fruit = new Fruit();
        $fruit->setId(1);

        // Mock findById to return the fruit before removal and null after removal
        $this->fruitRepositoryMock->expects($this->exactly(2))
            ->method('find')
            ->with(1)
            ->willReturnOnConsecutiveCalls($fruit, null);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($fruit));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // Assert that the fruit exists before removal
        $this->assertSame($fruit, $this->fruitCollection->findById(1));

        $this->fruitCollection->remove($fruit);

        // Assert that the fruit can't be found after removal
        $this->assertNull($this->fruitCollection->findById(1));
    }

    public function testRemoveInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Fruit.');

        $this->fruitCollection->remove(new \stdClass());
    }
}
