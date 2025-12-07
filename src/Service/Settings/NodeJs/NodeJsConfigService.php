<?php

declare(strict_types=1);

namespace App\Service\Settings\NodeJs;

use App\Service\Config\NodeJsPresetsServiceInterface;
use App\Service\Project\ProjectSessionServiceInterface;
use App\Service\Settings\AbstractWebDevConfigService;

class NodeJsConfigService extends AbstractWebDevConfigService implements NodeJsConfigServiceInterface
{
    public function __construct(
        private readonly NodeJsPresetsServiceInterface $nodeJsPresetsService,
        ProjectSessionServiceInterface $projectSessionService
    ) {
        parent::__construct($projectSessionService);
    }

    /**
     * Get NodeJS configuration
     */
    public function getNodeJsConfig(): array
    {
        $config = $this->getConfig();
        return $config['nodejs'] ?? [];
    }

    /**
     * Update NodeJS configuration
     */
    public function updateNodeJsConfig(array $data): void
    {
        $this->validateNodeJsConfig($data);
        
        if ($this->config === null) {
            $this->loadConfig();
        }

        $this->config['nodejs'] = array_merge($this->config['nodejs'] ?? [], $data);
        $this->saveConfig();
    }

    /**
     * Validate NodeJS configuration data
     */
    private function validateNodeJsConfig(array $data): void
    {
        if (isset($data['version'])) {
            $version = (int) $data['version'];
            $range = $this->nodeJsPresetsService->getNodeJsVersionRange();
            if ($version < $range['min'] || $version > $range['max']) {
                throw new \InvalidArgumentException("Invalid NodeJS version. Must be between {$range['min']} and {$range['max']}");
            }
        }
    }
}