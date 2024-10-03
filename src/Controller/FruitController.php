<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\DTO\FoodCreateDTO;
use App\DTO\FoodPaginationDTO;
use App\Service\ValidationService;
use App\Service\Collections\FruitCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\WeightUnit;

class FruitController extends AbstractController
{
    private FruitCollection $fruitCollection;
    private ValidationService $validationService;

    public function __construct(FruitCollection $fruitCollection, ValidationService $validationService)
    {
        $this->fruitCollection = $fruitCollection;
        $this->validationService = $validationService;
    }

    #[Route('/fruits', name: 'list_fruits', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $name = $request->query->get('name', '');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $unit = $request->query->get('unit', WeightUnit::GRAM);

        $foodPaginationDTO = new FoodPaginationDTO();
        $foodPaginationDTO->name = $name;
        $foodPaginationDTO->page = $page;
        $foodPaginationDTO->limit = $limit;
        $foodPaginationDTO->unit = $unit;

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
        $data = json_decode($request->getContent(), true);

        $foodDTO = new FoodCreateDTO($data['name'] ?? '', $data['quantity'] ?? 0);
        $errors = $this->validationService->validate($foodDTO);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $this->validationService->formatErrors($errors)], JsonResponse::HTTP_BAD_REQUEST);
        }

        $fruit = new Fruit();
        $fruit->setName($data['name']);
        $fruit->setQuantity($data['quantity']);

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
        return new JsonResponse(['status' => 'Fruit removed'], JsonResponse::HTTP_NO_CONTENT);
    }
}