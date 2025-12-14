<?php

declare(strict_types=1);

namespace App\Service\Settings\Tests;

interface TestConfigServiceInterface
{
    public function getTests(): array;

    public function getTest(string $key): ?array;

    /**
     * Get a specific test by key for a given project path
     *
     * @param string $projectPath The project path
     * @param string $key The test key
     * @return array<string, mixed> The test configuration
     * @throws \InvalidArgumentException If the test is not found
     * @throws \RuntimeException If the configuration file cannot be read or parsed
     */
    public function getTestForProject(string $projectPath, string $key): array;

    public function createTest(string $key, array $data): void;

    public function updateTest(string $key, array $data): void;

    public function deleteTest(string $key): void;
}
