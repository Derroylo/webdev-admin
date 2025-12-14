<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Project\ProjectServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetRunningProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
    ) {
    }

    #[Route('/projects/running', name: 'projects_running', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $basePath = $_ENV['PROJECTS_BASE_PATH'] ?? '';

        if (empty($basePath)) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'PROJECTS_BASE_PATH environment variable is not set',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Get all projects with running status marked
        $projects = $this->projectService->markRunningProject($basePath);

        // Find the running project
        $runningProject = null;
        foreach ($projects as $project) {
            if ($project->isProjectRunning) {
                $runningProject = [
                    'name'               => $project->name,
                    'path'               => $project->path,
                    'isWebdevCompatible' => $project->isWebdevCompatible,
                    'phpVersion'         => $project->config?->php->version ?? null,
                    'nodejsVersion'      => $project->config?->nodejs->version ?? null,
                    'schemaVersion'      => $project->config?->schemaVersion ?? null,
                ];
                break;
            }
        }

        return new JsonResponse([
            'success'        => true,
            'runningProject' => $runningProject,
        ]);
    }
}
