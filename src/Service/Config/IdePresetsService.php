<?php

declare(strict_types=1);

namespace App\Service\Config;

class IdePresetsService extends AbstractPresetsService implements IdePresetsServiceInterface
{
    /**
     * Get all IDE configs
     */
    public function getIdeConfigs(): array
    {
        return $this->loadConfig('ide');
    }

    /**
     * Get a specific IDE config
     */
    public function getIdeConfig(string $key): ?array
    {
        return $this->getIdeConfigs()[$key] ?? null;
    }
}