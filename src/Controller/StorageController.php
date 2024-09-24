<?php

namespace App\Controller;

use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StorageController extends AbstractController
{
    private $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * This function receives a json payload and processes it
     * TODO: Add validations to the json payload
     */

    #[Route('/process', name: 'process_request', methods: ['POST'])]
    public function process(Request $request): JsonResponse
    {
        $json = $request->getContent();
        $this->storageService->setRequest($json);
        $this->storageService->processRequest();

        return new JsonResponse(['status' => 'Request processed'], JsonResponse::HTTP_OK);
    }
}