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
     *
     * @return ProjectDto[] Array of ProjectDto objects
     */
    public function getAllProjects(string $basePath): array;

    /**
     * Get a project by its path
     *
     * @param string $projectPath Path to the project
     *
     * @return ProjectDto|null ProjectDto object if found, null otherwise
     */
    public function getProject(string $projectPath): ?ProjectDto;

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
     *
     * @return ProjectDto[] Array of ProjectDto objects
     */
    public function refreshProjects(string $basePath): array;

    /**
     * Mark which project is currently running by checking Docker containers
     *
     * @param string $basePath Base directory to scan for projects
     *
     * @return ProjectDto[] Array of ProjectDto objects with isProjectRunning property set
     */
    public function markRunningProject(string $basePath): array;
}
