<?php

namespace App\Controller;

use App\Entity\Vegetable;
use App\DTO\FoodCreateDTO;
use App\Service\ValidationService;
use App\DTO\FoodPaginationDTO;
use App\Service\Collections\VegetableCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\WeightUnit;

class VegetableController extends AbstractController
{
    private VegetableCollection $vegetableCollection;
    private ValidationService $validationService;
    
    public function __construct(VegetableCollection $vegetableCollection, ValidationService $validationService)
    {
        $this->vegetableCollection = $vegetableCollection;
        $this->validationService = $validationService;
    }

    #[Route('/vegetables', name: 'list_vegetables', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
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
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        list($vegetables, $totalItems) = $this->vegetableCollection->list($name, $page, $limit, $unit);

        $totalPages = ceil($totalItems / $limit);

        return $this->json([
            'data' => $vegetables,
            'meta' => [
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'items_per_page' => $limit,
            ],
        ]);
        return $this->json($vegetables);
    }

    #[Route('/vegetables', name: 'add_vegetable', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $foodDTO = new FoodCreateDTO();
        $foodDTO->name = $data['name'] ?? null;
        $foodDTO->quantity = $data['quantity'] ?? null;

        $errors = $this->validationService->validate($foodDTO);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        $vegetable = new Vegetable();
        $vegetable->setName($data['name']);
        $vegetable->setQuantity($data['quantity']);

        $this->vegetableCollection->add($vegetable);

        return $this->json($vegetable, JsonResponse::HTTP_CREATED);
    }

    #[Route('/vegetables/{id}', name: 'remove_vegetable', methods: ['DELETE'])]
    public function remove(int $id): JsonResponse
    {
        // Fetch the fruit entity by its ID
        $vegetable = $this->vegetableCollection->findById($id);

        if ($vegetable) {
            $this->vegetableCollection->remove($vegetable);
            return new JsonResponse(['status' => 'Vegetable removed'], JsonResponse::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['status' => 'Vegetable not found'], JsonResponse::HTTP_NOT_FOUND);
    }
}