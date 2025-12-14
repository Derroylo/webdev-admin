<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CurrentProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    /**
     * Get current project info
     */
    #[Route('/project/current', name: 'project_current', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $path = $this->projectSessionService->getCurrentProjectPath();

        if ($path === null) {
            return new JsonResponse([
                'selected' => false,
            ]);
        }

        return new JsonResponse([
            'selected' => true,
            'project'  => [
                'path'  => $path,
                'name'  => $this->projectSessionService->getProjectName(),
                'valid' => $this->projectSessionService->isValidProjectPath($path),
            ],
        ]);
    }
}
