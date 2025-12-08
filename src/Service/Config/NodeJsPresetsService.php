<?php

declare(strict_types=1);

namespace App\Service\Config;

class NodeJsPresetsService extends AbstractPresetsService implements NodeJsPresetsServiceInterface
{
    /**
     * Get all Node.js versions
     */
    public function getNodeJsVersions(): array
    {
        $versions = $this->loadConfig('versions');

        return $versions['nodejs']['versions'] ?? [];
    }

    /**
     * Get valid Node.js version numbers (for validation)
     */
    public function getValidNodeJsVersions(): array
    {
        return array_column($this->getNodeJsVersions(), 'version');
    }

    /**
     * Get Node.js version range
     */
    public function getNodeJsVersionRange(): array
    {
        $versions = $this->loadConfig('versions');

        return [
            'min' => $versions['nodejs']['min_version'] ?? 14,
            'max' => $versions['nodejs']['max_version'] ?? 22,
        ];
    }

    /**
     * Get default Node.js version
     */
    public function getDefaultNodeJsVersion(): int
    {
        $versions = $this->loadConfig('versions');

        return $versions['nodejs']['default_version'] ?? 20;
    }
}
