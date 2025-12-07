<?php

declare(strict_types=1);

namespace App\Controller\Logs\Api;

use App\Service\LogParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetLogsApiController extends AbstractController
{
    public function __construct(
        private readonly LogParserService $logParserService
    ) {
    }

    #[Route('/api/logs/{type}', name: 'app_api_logs')]
    public function __invoke(string $type): JsonResponse
    {
        try {
            $logs = $this->logParserService->parseLogs($type);
            
            return new JsonResponse([
                'data' => $logs,
                'recordsTotal' => count($logs),
                'recordsFiltered' => count($logs),
            ]);
        } catch (\RuntimeException|\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
