<?php

declare(strict_types=1);

namespace App\Service\Project;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ProjectDockerComposeService implements ProjectDockerComposeServiceInterface
{
    private const DOCKER_COMPOSE_FILE = '.devcontainer/docker-compose.yml';

    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    private function getCurrentProjectPath(): string
    {
        return $this->projectSessionService->getCurrentProjectPath() ?? throw new \RuntimeException('No project selected. Please select a project first.');
    }

    public function getDockerComposeFileContent(): array
    {
        $dockerComposeFile = $this->getCurrentProjectPath() . '/' . self::DOCKER_COMPOSE_FILE;

        if (!file_exists($dockerComposeFile)) {
            throw new \RuntimeException('Docker compose file not found: ' . $dockerComposeFile);
        }

        try {
            return Yaml::parseFile($dockerComposeFile);
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to parse YAML file: ' . $dockerComposeFile . ': ' . $e->getMessage());
        }
    }

    public function saveDockerComposeFile(array $dockerComposeFileContent): void
    {
        $dockerComposeFile = $this->getCurrentProjectPath() . '/' . self::DOCKER_COMPOSE_FILE;

        if (!file_exists($dockerComposeFile)) {
            throw new \RuntimeException('Docker compose file not found: ' . $dockerComposeFile);
        }

        // Create backup before saving
        $backupPath = $dockerComposeFile . '.backup.' . date('YmdHis');
        copy($dockerComposeFile, $backupPath);

        try {
            file_put_contents($dockerComposeFile, Yaml::dump($dockerComposeFileContent, 4, 2));
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to save YAML file: ' . $dockerComposeFile . ': ' . $e->getMessage());
        }

        // Keep only last 5 backups
        $this->cleanupBackups();
    }

    public function getServices(): array
    {
        $dockerComposeFile = $this->getDockerComposeFileContent();

        return $dockerComposeFile['services'];
    }

    public function getService(string $serviceName): array
    {
        $dockerComposeFile = $this->getDockerComposeFileContent();

        return $dockerComposeFile['services'][$serviceName];
    }

    public function addService(string $serviceName, string $serviceDefinition): void
    {
        $dockerComposeFile = $this->getDockerComposeFileContent();

        // Normalize linebreaks to Unix (\n)
        $serviceDefinition = str_replace(["\r\n", "\r"], "\n", $serviceDefinition);

        // Parse the YAML service definition string into an array
        $parsedService = Yaml::parse($serviceDefinition);

        if (!is_array($parsedService)) {
            throw new \RuntimeException('Invalid service definition YAML.');
        }

        $dockerComposeFile['services'][$serviceName] = $parsedService;

        $this->saveDockerComposeFile($dockerComposeFile);
    }

    public function removeService(string $serviceName): void
    {
        $dockerComposeFile = $this->getDockerComposeFileContent();

        unset($dockerComposeFile['services'][$serviceName]);

        $this->saveDockerComposeFile($dockerComposeFile);
    }

    /**
     * Clean up old backup files, keeping only the last 5
     */
    protected function cleanupBackups(): void
    {
        $dockerComposeFile  = $this->getCurrentProjectPath() . '/' . self::DOCKER_COMPOSE_FILE;
        $backupFiles = glob($dockerComposeFile . '.backup.*');

        if (\count($backupFiles) > 5) {
            // Sort by modification time, oldest first
            usort($backupFiles, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Remove oldest backups
            $toRemove = \array_slice($backupFiles, 0, \count($backupFiles) - 5);
            foreach ($toRemove as $file) {
                unlink($file);
            }
        }
    }
}