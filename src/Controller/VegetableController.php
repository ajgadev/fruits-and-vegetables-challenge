<?php

namespace App\Controller;

use App\Entity\Vegetable;
use App\DTO\FoodCreateDTO;
use App\Service\ValidationService;
use App\DTO\FoodPaginationDTO;
use App\Service\Collections\VegetableCollection;
use App\Service\FoodService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\WeightUnit;

use Symfony\Component\Serializer\SerializerInterface;

class VegetableController extends AbstractController
{
    private VegetableCollection $vegetableCollection;
    private ValidationService $validationService;
    private FoodService $foodService;
    private SerializerInterface $serializer;

    public function __construct(VegetableCollection $vegetableCollection, ValidationService $validationService, FoodService $foodService, SerializerInterface $serializer)
    {
        $this->vegetableCollection = $vegetableCollection;
        $this->validationService = $validationService;
        $this->foodService = $foodService;
        $this->serializer = $serializer;
    }

    #[Route('/vegetables', name: 'list_vegetables', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $unit = $request->query->get('unit', WeightUnit::GRAM);

        $foodPaginationDTO = new FoodPaginationDTO($name, $page, $limit, $unit);

        $errors = $this->validationService->validate($foodPaginationDTO);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $this->validationService->formatErrors($errors)], JsonResponse::HTTP_BAD_REQUEST);
        }

        list($vegetables, $totalItems, $totalPages) = $this->vegetableCollection->list($name, $page, $limit, $unit);

        return $this->json([
            'data' => $vegetables,
            'meta' => [
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'items_per_page' => $limit,
            ],
        ]);
    }

    #[Route('/vegetables', name: 'add_vegetable', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        // Deserialize the request body into a DTO object
        $foodDTO = $this->serializer->deserialize($request->getContent(), FoodCreateDTO::class, 'json');

        $errors = $this->validationService->validate($foodDTO);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $this->validationService->formatErrors($errors)], JsonResponse::HTTP_BAD_REQUEST);
        }

        $vegetable = $this->foodService->createFood(Vegetable::class, $foodDTO->getName(), $foodDTO->getQuantity());

        $this->vegetableCollection->add($vegetable);

        return $this->json($vegetable, JsonResponse::HTTP_CREATED);
    }

    #[Route('/vegetables/{id}', name: 'remove_vegetable', methods: ['DELETE'])]
    public function remove(int $id): JsonResponse
    {
        // Fetch the fruit entity by its ID
        $vegetable = $this->vegetableCollection->findById($id);

        if (!$vegetable) {
            return new JsonResponse(['message' => 'Vegetable not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $this->vegetableCollection->remove($vegetable);
        return new JsonResponse(['message' => 'Vegetable removed'], JsonResponse::HTTP_OK);
    }
}
