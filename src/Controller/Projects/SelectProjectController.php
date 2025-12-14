<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SelectProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    /**
     * Set the current project path
     */
    #[Route('/project/select', name: 'project_select', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $path = $request->request->get('path');

        if (empty($path)) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'Path is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Normalize the path
        $path = rtrim($path, '/');

        // Check if directory exists
        if (!is_dir($path)) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'Directory does not exist: ' . $path,
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate that it contains a webdev.yml config
        if (!$this->projectSessionService->isValidProjectPath($path)) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'Selected directory does not contain a valid .devcontainer/webdev.yml configuration file',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Store in session
        $this->projectSessionService->setCurrentProjectPath($path);

        return new JsonResponse([
            'success' => true,
            'project' => [
                'path' => $path,
                'name' => basename($path),
            ],
        ]);
    }
}
