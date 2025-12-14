<?php

declare(strict_types=1);

namespace App\Controller\Projects\Tasks;

use App\Service\Command\CommandExecutionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExecuteCommandController extends AbstractController
{
    public function __construct(
        private readonly CommandExecutionServiceInterface $commandExecutionService,
    ) {
    }

    #[Route('/projects/commands/execute', name: 'projects_commands_execute', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $projectPath = $request->request->get('projectPath');
        $command = $request->request->get('command');

        if (empty($projectPath) || empty($command)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'projectPath and command are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->commandExecutionService->executeCommand('webdev-prerelease ' . $command, $projectPath);

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
