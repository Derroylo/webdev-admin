<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Dto\TestExecutionRequestDto;
use App\Service\Test\TestExecutionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExecuteTestController extends AbstractController
{
    public function __construct(
        private readonly TestExecutionServiceInterface $testExecutionService,
    ) {
    }

    #[Route('/projects/tests/execute', name: 'projects_test_execute', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $projectPath = $request->request->get('projectPath');
        $testKey = $request->request->get('testKey');

        if (empty($projectPath) || empty($testKey)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'projectPath and testKey are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $executionRequest = TestExecutionRequestDto::fromArray([
                'projectPath' => $projectPath,
                'testKey' => $testKey,
            ]);

            $result = $this->testExecutionService->executeTest($executionRequest);

            return new JsonResponse($result->toArray(), Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\RuntimeException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
