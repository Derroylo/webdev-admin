<?php

declare(strict_types=1);

namespace App\Service\Config;

class PhpPresetsService extends AbstractPresetsService implements PhpPresetsServiceInterface
{
    /**
     * Get all PHP versions
     */
    public function getPhpVersions(): array
    {
        $versions = $this->loadConfig('versions');
        return $versions['php']['versions'] ?? [];
    }

    /**
     * Get valid PHP version strings (for validation)
     */
    public function getValidPhpVersions(): array
    {
        return array_column($this->getPhpVersions(), 'version');
    }

    /**
     * Get default PHP version
     */
    public function getDefaultPhpVersion(): string
    {
        $versions = $this->loadConfig('versions');

        return $versions['php']['default_version'] ?? '8.3';
    }
}