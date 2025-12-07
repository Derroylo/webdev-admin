<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Config\IdePresetsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

class OpenIdeController extends AbstractController
{
    public function __construct(
        private readonly IdePresetsServiceInterface $idePresetsService
    ) {
    }

    #[Route('/projects/ide/open', name: 'projects_ide_open', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $projectPath = $request->request->get('projectPath');
        $ide = $request->request->get('ide');
        
        if (empty($projectPath) || empty($ide)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'projectPath and ide are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate project path
        if (empty($projectPath) || !is_dir($projectPath)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid project path',
            ], Response::HTTP_BAD_REQUEST);
        }

        $ideConfig = $this->idePresetsService->getIdeConfig($ide);
        if ($ideConfig === null) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Unknown IDE: ' . $ide,
            ], Response::HTTP_BAD_REQUEST);
        }

        $command = $ideConfig['command'];

        if (empty($command)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'No command found for IDE: ' . $ideConfig['name'],
            ], Response::HTTP_BAD_REQUEST);
        }

        // Handle different IDEs
        return $this->openIde($projectPath, $command, $ideConfig['name']);
    }

    private function openIde(string $projectPath, string $command, string $name): JsonResponse
    {
        // Execute "cursor ." in the project directory
        $process = Process::fromShellCommandline($command);
        $process->setWorkingDirectory($projectPath);
        $process->setTimeout(10); // Short timeout for opening IDE
        
        try {
            // Run the process (non-blocking, fire and forget)
            $process->start();
            $process->wait();

            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            if ($process->isSuccessful()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Opening project in ' . $name . '... ' . $output . ' ' . $errorOutput,
                ]);
            }
            
            return new JsonResponse([
                'success' => false,
                'error' => 'Failed to open ' . $name . ': ' . $errorOutput,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Failed to open ' . $name . ': ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

