<?php

declare(strict_types=1);

namespace App\Service\Settings\Php;

use App\Service\Config\PhpPresetsServiceInterface;
use App\Service\Project\ProjectSessionServiceInterface;
use App\Service\Settings\AbstractWebDevConfigService;

class PhpConfigService extends AbstractWebDevConfigService implements PhpConfigServiceInterface
{
    public function __construct(
        private readonly PhpPresetsServiceInterface $phpPresetsService,
        ProjectSessionServiceInterface $projectSessionService,
    ) {
        parent::__construct($projectSessionService);
    }

    /**
     * Get PHP configuration
     */
    public function getPhpConfig(): array
    {
        $config = $this->getConfig();

        return $config['php'] ?? [];
    }

    /**
     * Update PHP configuration
     */
    public function updatePhpConfig(array $data): void
    {
        $this->validatePhpConfig($data);

        if ($this->config === null) {
            $this->loadConfig();
        }

        $this->config['php'] = array_merge($this->config['php'] ?? [], $data);
        $this->saveConfig();
    }

    /**
     * Validate PHP configuration data
     */
    private function validatePhpConfig(array $data): void
    {
        if (isset($data['version'])) {
            $validVersions = $this->phpPresetsService->getValidPhpVersions();

            if (!\in_array($data['version'], $validVersions, true)) {
                throw new \InvalidArgumentException('Invalid PHP version. Must be one of: ' . implode(', ', $validVersions));
            }
        }
    }
}
