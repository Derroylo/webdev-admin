<?php

declare(strict_types=1);

namespace App\Service\Settings\Tests;

use App\Service\Settings\AbstractWebDevConfigService;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

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
     * Get a specific test by key for a given project path
     *
     * @param string $projectPath The project path
     * @param string $key The test key
     * @return array<string, mixed> The test configuration
     * @throws \InvalidArgumentException If the test is not found
     * @throws \RuntimeException If the configuration file cannot be read or parsed
     */
    public function getTestForProject(string $projectPath, string $key): array
    {
        $config = $this->loadConfigForProject($projectPath);

        if (!isset($config['tests']) || !\is_array($config['tests'])) {
            throw new \InvalidArgumentException('No tests found in webdev.yml');
        }

        if (!isset($config['tests'][$key]) || !\is_array($config['tests'][$key])) {
            throw new \InvalidArgumentException("Test '{$key}' not found");
        }

        return $config['tests'][$key];
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
     * Load configuration from webdev.yml file for a specific project path
     *
     * @param string $projectPath The project path
     * @return array<string, mixed>
     * @throws \RuntimeException If the configuration file cannot be read or parsed
     */
    private function loadConfigForProject(string $projectPath): array
    {
        $configPath = $projectPath . '/' . self::CONFIG_FILE;

        if (!file_exists($configPath) || !is_readable($configPath)) {
            throw new \RuntimeException('webdev.yml not found or not readable');
        }

        try {
            $config = Yaml::parseFile($configPath);
        } catch (ParseException $e) {
            throw new \RuntimeException('Failed to parse webdev.yml: ' . $e->getMessage(), 0, $e);
        }

        if (!\is_array($config)) {
            throw new \RuntimeException('Invalid configuration format');
        }

        return $config;
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
