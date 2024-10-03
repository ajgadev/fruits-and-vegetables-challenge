<?php

namespace App\Controller;

use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Psr\Log\LoggerInterface;
class StorageController extends AbstractController
{
    private $storageService;
    private LoggerInterface $logger;

    public function __construct(StorageService $storageService, LoggerInterface $logger)
    {
        $this->storageService = $storageService;
        $this->logger = $logger;
    }

    /**
     * This function receives a json payload and processes it
     * TODO: Add validations to the json payload
     */

    #[Route('/process', name: 'process_request', methods: ['POST'])]
    public function process(Request $request): JsonResponse
    {
        $json = $request->getContent();

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate further according to expected structure
        // if (!$this->isValidJsonStructure($data)) {
        //     return new JsonResponse(['error' => 'Invalid JSON structure'], JsonResponse::HTTP_BAD_REQUEST);
        // }
        try {
            $this->storageService->setRequest($json);
            $this->storageService->processRequest();
            return new JsonResponse(['status' => 'Request processed'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Processing failed', ['exception' => $e]);
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function isValidJsonStructure(array $data): bool
    {
        // Implement checks for your expected JSON structure
        return isset($data['requiredField']);
    }
}
