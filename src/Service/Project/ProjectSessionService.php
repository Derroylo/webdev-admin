<?php

declare(strict_types=1);

namespace App\Service\Project;

use Symfony\Component\HttpFoundation\RequestStack;

class ProjectSessionService implements ProjectSessionServiceInterface
{
    private const SESSION_KEY = 'current_project_path';
    private const CONFIG_FILE = '.devcontainer/webdev.yml';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Get the current project path from session
     */
    public function getCurrentProjectPath(): ?string
    {
        return $this->getSession()->get(self::SESSION_KEY);
    }

    /**
     * Set the current project path in session
     */
    public function setCurrentProjectPath(string $path): void
    {
        $this->getSession()->set(self::SESSION_KEY, $path);
    }

    /**
     * Clear the current project path from session
     */
    public function clearCurrentProject(): void
    {
        $this->getSession()->remove(self::SESSION_KEY);
    }

    /**
     * Check if a project is currently selected
     */
    public function hasProjectSelected(): bool
    {
        $path = $this->getCurrentProjectPath();

        return $path !== null && $this->isValidProjectPath($path);
    }

    /**
     * Validate that a path contains a valid devcontainer config
     */
    public function isValidProjectPath(string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $configPath = rtrim($path, '/') . '/' . self::CONFIG_FILE;

        return file_exists($configPath) && is_readable($configPath);
    }

    /**
     * Get the project name from the path (last directory name)
     */
    public function getProjectName(): ?string
    {
        $path = $this->getCurrentProjectPath();

        if ($path === null) {
            return null;
        }

        return basename(rtrim($path, '/'));
    }

    /**
     * Get the config file path for the current project
     */
    public function getConfigFilePath(): ?string
    {
        $path = $this->getCurrentProjectPath();

        if ($path === null) {
            return null;
        }

        return rtrim($path, '/') . '/' . self::CONFIG_FILE;
    }

    private function getSession(): \Symfony\Component\HttpFoundation\Session\SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new \RuntimeException('No current request available');
        }

        return $request->getSession();
    }
}
