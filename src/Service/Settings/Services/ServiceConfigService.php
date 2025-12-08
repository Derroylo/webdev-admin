<?php

declare(strict_types=1);

namespace App\Service\Settings\Services;

use App\Service\Config\ServicePresetsServiceInterface;
use App\Service\Project\ProjectSessionServiceInterface;
use App\Service\Settings\AbstractWebDevConfigService;

class ServiceConfigService extends AbstractWebDevConfigService implements ServiceConfigServiceInterface
{
    public function __construct(
        private readonly ServicePresetsServiceInterface $servicePresetsService,
        ProjectSessionServiceInterface $projectSessionService,
    ) {
        parent::__construct($projectSessionService);
    }

    /**
     * Get all services
     */
    public function getServices(): array
    {
        $config = $this->getConfig();

        return $config['services'] ?? [];
    }

    /**
     * Get a specific service by key
     */
    public function getService(string $key): ?array
    {
        $services = $this->getServices();

        return $services[$key] ?? null;
    }

    /**
     * Create a new service
     */
    public function createService(string $key, array $data): void
    {
        $this->validateServiceData($data);

        if ($this->config === null) {
            $this->loadConfig();
        }

        if (isset($this->config['services'][$key])) {
            throw new \InvalidArgumentException("Service with key '{$key}' already exists");
        }

        if (!isset($this->config['services'])) {
            $this->config['services'] = [];
        }

        $this->config['services'][$key] = $data;
        $this->saveConfig();
    }

    /**
     * Update an existing service
     */
    public function updateService(string $key, array $data): void
    {
        $this->validateServiceData($data);

        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['services'][$key])) {
            throw new \InvalidArgumentException("Service with key '{$key}' does not exist");
        }

        $this->config['services'][$key] = array_merge($this->config['services'][$key], $data);
        $this->saveConfig();
    }

    /**
     * Delete a service
     */
    public function deleteService(string $key): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['services'][$key])) {
            throw new \InvalidArgumentException("Service with key '{$key}' does not exist");
        }

        unset($this->config['services'][$key]);
        $this->saveConfig();
    }

    /**
     * Toggle service active status
     */
    public function toggleService(string $key, bool $active): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['services'][$key])) {
            throw new \InvalidArgumentException("Service with key '{$key}' does not exist");
        }

        $this->config['services'][$key]['active'] = $active;
        $this->saveConfig();
    }

    /**
     * Validate service data
     */
    private function validateServiceData(array $data): void
    {
        $required = ['name', 'category'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Service field '{$field}' is required");
            }
        }

        if (isset($data['port'])) {
            $port = (int) $data['port'];

            if ($port < 1 || $port > 65535) {
                throw new \InvalidArgumentException('Invalid port number. Must be between 1 and 65535');
            }
        }

        $validCategories = $this->servicePresetsService->getValidServiceCategories();

        if (!\in_array($data['category'], $validCategories, true)) {
            throw new \InvalidArgumentException('Invalid category. Must be one of: ' . implode(', ', $validCategories));
        }
    }
}
