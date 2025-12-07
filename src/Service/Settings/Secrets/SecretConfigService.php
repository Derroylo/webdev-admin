<?php

declare(strict_types=1);

namespace App\Service\Settings\Secrets;

use App\Service\Settings\AbstractWebDevConfigService;

class SecretConfigService extends AbstractWebDevConfigService implements SecretConfigServiceInterface
{
    /**
     * Get all secrets
     */
    public function getSecrets(): array
    {
        $config = $this->getConfig();
        return $config['secrets'] ?? [];
    }

    /**
     * Get a specific secret by key
     */
    public function getSecret(string $key): ?array
    {
        $secrets = $this->getSecrets();
        return $secrets[$key] ?? null;
    }

    /**
     * Create a new secret
     */
    public function createSecret(string $key, array $data): void
    {
        $this->validateSecretData($data);
        
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (isset($this->config['secrets'][$key])) {
            throw new \InvalidArgumentException("Secret with key '{$key}' already exists");
        }

        if (!isset($this->config['secrets'])) {
            $this->config['secrets'] = [];
        }

        $this->config['secrets'][$key] = $data;
        $this->saveConfig();
    }

    /**
     * Update an existing secret
     */
    public function updateSecret(string $key, array $data): void
    {
        $this->validateSecretData($data);
        
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['secrets'][$key])) {
            throw new \InvalidArgumentException("Secret with key '{$key}' does not exist");
        }

        $this->config['secrets'][$key] = array_merge($this->config['secrets'][$key], $data);
        $this->saveConfig();
    }

    /**
     * Delete a secret
     */
    public function deleteSecret(string $key): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['secrets'][$key])) {
            throw new \InvalidArgumentException("Secret with key '{$key}' does not exist");
        }

        unset($this->config['secrets'][$key]);
        $this->saveConfig();
    }

    /**
     * Validate secret data
     */
    private function validateSecretData(array $data): void
    {
        // Source key is required
        if (!isset($data['source']['key']) || empty($data['source']['key'])) {
            throw new \InvalidArgumentException("Secret source key is required");
        }

        // Either target file or envVar must be present
        if (!isset($data['target']['file']) && !isset($data['target']['envVar'])) {
            throw new \InvalidArgumentException("Secret must have either target file or envVar");
        }
    }
}