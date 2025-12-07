<?php

declare(strict_types=1);

namespace App\Service\Settings\Workspaces;

use App\Service\Settings\AbstractWebDevConfigService;

class WorkspaceConfigService extends AbstractWebDevConfigService implements WorkspaceConfigServiceInterface
{
    /**
     * Get all workspaces
     */
    public function getWorkspaces(): array
    {
        $config = $this->getConfig();
        return $config['workspaces'] ?? [];
    }

    /**
     * Get a specific workspace by key
     */
    public function getWorkspace(string $key): ?array
    {
        $workspaces = $this->getWorkspaces();
        return $workspaces[$key] ?? null;
    }

    /**
     * Create a new workspace
     */
    public function createWorkspace(string $key, array $data): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (isset($this->config['workspaces'][$key])) {
            throw new \InvalidArgumentException("Workspace with key '{$key}' already exists");
        }

        if (!isset($this->config['workspaces'])) {
            $this->config['workspaces'] = [];
        }

        $this->config['workspaces'][$key] = $data;
        $this->saveConfig();
    }

    /**
     * Update an existing workspace
     */
    public function updateWorkspace(string $key, array $data): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['workspaces'][$key])) {
            throw new \InvalidArgumentException("Workspace with key '{$key}' does not exist");
        }

        $this->config['workspaces'][$key] = array_merge($this->config['workspaces'][$key], $data);
        $this->saveConfig();
    }

    /**
     * Delete a workspace
     */
    public function deleteWorkspace(string $key): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['workspaces'][$key])) {
            throw new \InvalidArgumentException("Workspace with key '{$key}' does not exist");
        }

        unset($this->config['workspaces'][$key]);
        $this->saveConfig();
    }
}