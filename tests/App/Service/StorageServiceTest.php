<?php

namespace App\Tests\App\Service;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Service\Collections\FruitCollection;
use App\Service\Collections\VegetableCollection;
use App\Service\StorageService;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class StorageServiceTest extends TestCase
{
    /** @var FruitCollection&MockObject */
    private $fruitCollectionMock;
    /** @var VegetableCollection&MockObject */
    private $vegetableCollectionMock;
    private StorageService $storageService;

    protected function setUp(): void
    {
        $this->fruitCollectionMock = $this->createMock(FruitCollection::class);
        $this->vegetableCollectionMock = $this->createMock(VegetableCollection::class);
        $request = file_get_contents('request.json');
        $this->storageService = new StorageService(
            $this->fruitCollectionMock,
            $this->vegetableCollectionMock,
            $request
        );
    }

    public function testReceivingRequest(): void
    {
        $this->assertNotEmpty($this->storageService->getRequest());
        $this->assertIsString($this->storageService->getRequest());
    }

    public function testProcessRequest(): void
    {
        // Expect method calls for adding items to the respective collections
        $this->fruitCollectionMock
            ->expects($this->exactly(10))
            ->method('add')
            ->with($this->isInstanceOf(Fruit::class));
        $this->vegetableCollectionMock
            ->expects($this->exactly(10))
            ->method('add')
            ->with($this->isInstanceOf(Vegetable::class));

        // Verify specific items added to the collection
        $this->fruitCollectionMock
            ->method('add')
            ->will($this->returnCallback(function (Fruit $fruit) {
                if ($fruit->getName() === 'Apples') {
                    $this->assertEquals(20000, $fruit->getQuantity()); // 20 kg => 20000 g
                }
            }));

        $this->vegetableCollectionMock
            ->method('add')
            ->will($this->returnCallback(function (Vegetable $vegetable) {
                if ($vegetable->getName() === 'Carrot') {
                    $this->assertEquals(10922, $vegetable->getQuantity()); // 10922 g => 10922 g
                }
            }));

        // Process the request
        $this->storageService->processRequest();
    }
}
