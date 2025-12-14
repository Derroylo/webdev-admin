<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ClearProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    /**
     * Clear current project selection
     */
    #[Route('/project/clear', name: 'project_clear', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        $this->projectSessionService->clearCurrentProject();

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
