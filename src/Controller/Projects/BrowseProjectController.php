<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BrowseProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    /**
     * Browse directories for project selection
     */
    #[Route('/project/browse', name: 'project_browse', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
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
