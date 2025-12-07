<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Dto\ProjectDto;

interface ProjectServiceInterface
{
    /**
     * Get all projects from the given base path
     * Results are cached for performance
     *
     * @param string $basePath Base directory to scan for projects
     * @return ProjectDto[] Array of ProjectDto objects
     */
    public function getAllProjects(string $basePath): array;

    /**
     * Clear cache for projects
     *
     * @param string|null $basePath If provided, clear cache only for this base path. If null, clear all project caches.
     */
    public function clearCache(?string $basePath = null): void;

    /**
     * Force refresh projects (bypass cache)
     *
     * @param string $basePath Base directory to scan for projects
     * @return ProjectDto[] Array of ProjectDto objects
     */
    public function refreshProjects(string $basePath): array;
}

