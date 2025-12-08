<?php

declare(strict_types=1);

namespace App\Service\Settings\Tests;

use App\Service\Settings\AbstractWebDevConfigService;

class TestConfigService extends AbstractWebDevConfigService implements TestConfigServiceInterface
{
    /**
     * Get all tests
     */
    public function getTests(): array
    {
        $config = $this->getConfig();

        return $config['tests'] ?? [];
    }

    /**
     * Get a specific test by key
     */
    public function getTest(string $key): ?array
    {
        $tests = $this->getTests();

        return $tests[$key] ?? null;
    }

    /**
     * Create a new test
     */
    public function createTest(string $key, array $data): void
    {
        $this->validateTestData($data);

        if ($this->config === null) {
            $this->loadConfig();
        }

        if (isset($this->config['tests'][$key])) {
            throw new \InvalidArgumentException("Test with key '{$key}' already exists");
        }

        if (!isset($this->config['tests'])) {
            $this->config['tests'] = [];
        }

        $this->config['tests'][$key] = $data;
        $this->saveConfig();
    }

    /**
     * Update an existing test
     */
    public function updateTest(string $key, array $data): void
    {
        $this->validateTestData($data);

        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['tests'][$key])) {
            throw new \InvalidArgumentException("Test with key '{$key}' does not exist");
        }

        $this->config['tests'][$key] = array_merge($this->config['tests'][$key], $data);
        $this->saveConfig();
    }

    /**
     * Delete a test
     */
    public function deleteTest(string $key): void
    {
        if ($this->config === null) {
            $this->loadConfig();
        }

        if (!isset($this->config['tests'][$key])) {
            throw new \InvalidArgumentException("Test with key '{$key}' does not exist");
        }

        unset($this->config['tests'][$key]);
        $this->saveConfig();
    }

    /**
     * Validate test data
     */
    private function validateTestData(array $data): void
    {
        $required = ['name', 'commands'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Test field '{$field}' is required");
            }
        }

        if (!\is_array($data['commands'])) {
            throw new \InvalidArgumentException("Test field 'commands' must be an array");
        }
    }
}
