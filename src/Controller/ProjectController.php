<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Config\IdePresetsServiceInterface;
use App\Service\Project\ProjectServiceInterface;
use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
        private readonly ProjectServiceInterface $projectService,
        private readonly IdePresetsServiceInterface $idePresetsService,
    ) {
    }

    /**
     * Set the current project path
     */
    #[Route('/select', name: 'project_select', methods: ['POST'])]
    public function select(Request $request): JsonResponse
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

    /**
     * Get current project info
     */
    #[Route('/current', name: 'project_current', methods: ['GET'])]
    public function current(): JsonResponse
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

    /**
     * Clear current project selection
     */
    #[Route('/clear', name: 'project_clear', methods: ['POST'])]
    public function clear(): JsonResponse
    {
        $this->projectSessionService->clearCurrentProject();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Get list of webdev-compatible projects
     */
    #[Route('/list', name: 'project_list', methods: ['GET'])]
    public function list(): JsonResponse
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
                'phpVersion'    => $project->phpVersion,
                'nodejsVersion' => $project->nodejsVersion,
            ];
        }, $compatibleProjects);

        return new JsonResponse([
            'success'    => true,
            'projects'   => array_values($projects),
            'ideConfigs' => $this->idePresetsService->getIdeConfigs(),
        ]);
    }

    /**
     * Browse directories for project selection
     */
    #[Route('/browse', name: 'project_browse', methods: ['GET'])]
    public function browse(Request $request): JsonResponse
    {
        $path = $request->query->get('path', '/');

        // Security: prevent directory traversal
        $realPath = realpath($path);

        if ($realPath === false || !is_dir($realPath)) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'Invalid directory path',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Get list of directories
        $directories = [];
        $items       = @scandir($realPath);

        if ($items === false) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'Cannot read directory',
            ], Response::HTTP_FORBIDDEN);
        }

        foreach ($items as $item) {
            // Skip hidden files/folders and special entries
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $realPath . '/' . $item;

            // Only include directories
            if (!is_dir($fullPath)) {
                continue;
            }

            // Skip hidden directories (starting with .)
            if (str_starts_with($item, '.')) {
                continue;
            }

            // Check if this directory is readable
            if (!is_readable($fullPath)) {
                continue;
            }

            $hasConfig = $this->projectSessionService->isValidProjectPath($fullPath);

            $directories[] = [
                'name'       => $item,
                'path'       => $fullPath,
                'hasConfig'  => $hasConfig,
                'hasSubdirs' => $this->hasSubdirectories($fullPath),
            ];
        }

        // Sort directories alphabetically
        usort($directories, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        // Get parent directory
        $parentPath = \dirname($realPath);
        $canGoUp    = $parentPath !== $realPath && is_readable($parentPath);

        return new JsonResponse([
            'success'     => true,
            'currentPath' => $realPath,
            'parentPath'  => $canGoUp ? $parentPath : null,
            'canGoUp'     => $canGoUp,
            'directories' => $directories,
            'hasConfig'   => $this->projectSessionService->isValidProjectPath($realPath),
        ]);
    }

    /**
     * Check if a directory has any subdirectories
     */
    private function hasSubdirectories(string $path): bool
    {
        $items = @scandir($path);

        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (is_dir($path . '/' . $item)) {
                return true;
            }
        }

        return false;
    }
}
