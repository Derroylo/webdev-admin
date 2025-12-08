<?php

declare(strict_types=1);

namespace App\Service\Settings\Workspaces;

interface WorkspaceConfigServiceInterface
{
    public function getWorkspaces(): array;

    public function getWorkspace(string $key): ?array;

    public function createWorkspace(string $key, array $data): void;

    public function updateWorkspace(string $key, array $data): void;

    public function deleteWorkspace(string $key): void;
}
