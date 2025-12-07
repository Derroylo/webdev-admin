<?php

declare(strict_types=1);

namespace App\Service\Project;

interface ProjectSessionServiceInterface
{
    public function getCurrentProjectPath(): ?string;
    public function setCurrentProjectPath(string $path): void;
    public function clearCurrentProject(): void;
    public function hasProjectSelected(): bool;
    public function isValidProjectPath(string $path): bool;
    public function getProjectName(): ?string;
    public function getConfigFilePath(): ?string;
}