<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\DTO\FoodCreateDTO;
use App\DTO\FoodPaginationDTO;
use App\Entity\Food;
use App\Service\ValidationService;
use App\Service\Collections\FruitCollection;
use App\Service\FoodService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\WeightUnit;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class FruitController extends AbstractController
{
    private FruitCollection $fruitCollection;
    private ValidationService $validationService;
    private FoodService $foodService;
    private SerializerInterface $serializer;

    public function __construct(FruitCollection $fruitCollection, ValidationService $validationService, FoodService $foodService, SerializerInterface $serializer)
    {
        $this->fruitCollection = $fruitCollection;
        $this->validationService = $validationService;
        $this->foodService = $foodService;
        $this->serializer = $serializer;
    }

    #[Route('/fruits', name: 'list_fruits', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $name = $request->query->get('name', '');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $unit = $request->query->get('unit', WeightUnit::GRAM);

        $foodPaginationDTO = new FoodPaginationDTO($name, $page, $limit, $unit);

        $errors = $this->validationService->validate($foodPaginationDTO);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $this->validationService->formatErrors($errors)], JsonResponse::HTTP_BAD_REQUEST);
        }

        list($fruits, $totalItems) = $this->fruitCollection->list($name, $page, $limit, $unit);
        
        $totalPages = ceil($totalItems / $limit);

        return $this->json([
            'data' => $fruits,
            'meta' => [
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'items_per_page' => $limit,
            ],
        ]);
    }

    #[Route('/fruits', name: 'add_fruit', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $foodDTO = $this->serializer->deserialize($request->getContent(), FoodCreateDTO::class, 'json');
        $errors = $this->validationService->validate($foodDTO);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $this->validationService->formatErrors($errors)], JsonResponse::HTTP_BAD_REQUEST);
        }

        $fruit = $this->foodService->createFood(Fruit::class, $foodDTO->getName(), $foodDTO->getQuantity());

        $this->fruitCollection->add($fruit);

        return $this->json($fruit, JsonResponse::HTTP_CREATED);
    }

    #[Route('/fruits/{id}', name: 'remove_fruit', methods: ['DELETE'])]
    public function remove(int $id): JsonResponse
    {
        // Fetch the fruit entity by its ID
        $fruit = $this->fruitCollection->findById($id);
        if (!$fruit) {
            return new JsonResponse(['status' => 'Fruit not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $this->fruitCollection->remove($fruit);
        return new JsonResponse(['message' => 'Fruit removed'], JsonResponse::HTTP_OK);
    }
}