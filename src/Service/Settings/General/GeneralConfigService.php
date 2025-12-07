<?php

declare(strict_types=1);

namespace App\Service\Settings\General;

use App\Service\Settings\AbstractWebDevConfigService;

class GeneralConfigService extends AbstractWebDevConfigService implements GeneralConfigServiceInterface
{
    /**
     * Get general configuration
     */
    public function getGeneralConfig(): array
    {
        $config = $this->getConfig();
        return $config['config'] ?? [];
    }

    /**
     * Update general configuration
     */
    public function updateGeneralConfig(array $data): void
    {
        $this->validateGeneralConfig($data);
        
        if ($this->config === null) {
            $this->loadConfig();
        }

        $this->config['config'] = array_merge($this->config['config'] ?? [], $data);
        $this->saveConfig();
    }

    /**
     * Validate general configuration data
     */
    private function validateGeneralConfig(array $data): void
    {
        if (isset($data['allowPreReleases']) && !is_bool($data['allowPreReleases'])) {
            throw new \InvalidArgumentException("allowPreReleases must be a boolean value");
        }

        if (isset($data['workspaceFolder']) && (!is_string($data['workspaceFolder']) || empty($data['workspaceFolder']))) {
            throw new \InvalidArgumentException("workspaceFolder must be a non-empty string");
        }

        if (isset($data['proxy'])) {
            if (!is_array($data['proxy'])) {
                throw new \InvalidArgumentException("proxy must be an array");
            }

            if (isset($data['proxy']['domain']) && (!is_string($data['proxy']['domain']) || empty($data['proxy']['domain']))) {
                throw new \InvalidArgumentException("proxy.domain must be a non-empty string");
            }

            if (isset($data['proxy']['subDomain']) && (!is_string($data['proxy']['subDomain']) || empty($data['proxy']['subDomain']))) {
                throw new \InvalidArgumentException("proxy.subDomain must be a non-empty string");
            }
        }
    }
}
