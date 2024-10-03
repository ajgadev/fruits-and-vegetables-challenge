<?php 

namespace App\Tests\Service;

use App\DTO\FoodCreateDTO;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidationServiceTest extends KernelTestCase
{
    private ValidationService $validationService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $validator = $container->get('validator');
        $this->validationService = new ValidationService($validator);
    }

    public function testValidateValidDTO()
    {
        $foodDTO = new FoodCreateDTO('Banana', 200);
        $violations = $this->validationService->validate($foodDTO);
        $this->assertCount(0, $violations);
    }

    public function testValidateInvalidDTO()
    {
        $foodDTO = new FoodCreateDTO('', -100);
        $violations = $this->validationService->validate($foodDTO);
        $this->assertGreaterThan(0, count($violations));
    }
}