<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Config\IdePresetsServiceInterface;
use App\Service\Project\ProjectServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListProjectsController extends AbstractController
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
        private readonly IdePresetsServiceInterface $idePresetsService,
    ) {
    }

    /**
     * Get list of webdev-compatible projects
     */
    #[Route('/project/list', name: 'project_list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $basePath = $_ENV['PROJECTS_BASE_PATH'] ?? '';

        if (empty($basePath)) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'PROJECTS_BASE_PATH environment variable is not set',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Get all projects
        $allProjects = $this->projectService->getAllProjects($basePath);

        // Filter only webdev-compatible projects
        $compatibleProjects = array_filter($allProjects, fn ($project) => $project->isWebdevCompatible);

        // Convert to array format for JSON response
        $projects = array_map(function ($project) {
            return [
                'name'          => $project->name,
                'path'          => $project->path,
                'phpVersion'    => $project->config?->php->version,
                'nodejsVersion' => $project->config?->nodejs->version,
                'isWebdevCompatible' => $project->isWebdevCompatible,
                'isProjectRunning' => $project->isProjectRunning,
            ];
        }, $compatibleProjects);

        return new JsonResponse([
            'success'    => true,
            'projects'   => array_values($projects),
            'ideConfigs' => $this->idePresetsService->getIdeConfigs(),
        ]);
    }
}
