<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Dto\Project\AbstractProjectConfigDto;
use App\Dto\Project\Schema2\ProjectConfigDto as Schema2ProjectConfigDto;
use App\Dto\Project\Schema3\ProjectConfigDto as Schema3ProjectConfigDto;
use Symfony\Component\CssSelector\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ProjectConfigService implements ProjectConfigServiceInterface
{
    private const CONFIG_FILE = '.devcontainer/webdev.yml';

    private const DEVCONTAINER_FILE = '.devcontainer/devcontainer.json';


    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    public function getCurrentProjectConfig(): AbstractProjectConfigDto | null
    {
        $projectPath = $this->projectSessionService->getCurrentProjectPath();
        
        if ($projectPath === null) {
            throw new \RuntimeException('No project selected. Please select a project first.');
        }

        return $this->getProjectConfig($projectPath);
    }

    /**
     * Get the project config for a given project path
     */
    public function getProjectConfig(string $projectPath): AbstractProjectConfigDto | null
    {
        $configPath = $projectPath . '/' . self::CONFIG_FILE;

        if (!file_exists($configPath) || !is_readable($configPath)) {
            return null;
        }

        $config = null;

        try {
            $config = Yaml::parseFile($configPath);
        } catch (ParseException $e) {
            throw new \RuntimeException('Unable to parse YAML configuration: ' . $e->getMessage());
        }

        if (!\is_array($config)) {
            return null;
        }

        if (!isset($config['schemaVersion']) || $config['schemaVersion'] === 2) {
            return Schema2ProjectConfigDto::fromArray($config);
        }

        if (isset($config['schemaVersion']) && $config['schemaVersion'] === 3) {
            return Schema3ProjectConfigDto::fromArray($config);
        }

        return null;
    }

    public function validateAndSaveCurrentProjectConfig(AbstractProjectConfigDto $projectConfigDto): void
    {
        $projectPath = $this->projectSessionService->getCurrentProjectPath();
        
        if ($projectPath === null) {
            throw new \RuntimeException('No project selected. Please select a project first.');
        }

        $this->validateAndSaveProjectConfig($projectConfigDto, $projectPath);
    }

    public function validateAndSaveProjectConfig(AbstractProjectConfigDto $projectConfigDto, string $projectPath): void
    {
        $configPath = $this->getProjectConfigPath($projectPath);

        if (!is_writable($configPath)) {
            throw new \RuntimeException("Configuration file is not writable: {$configPath}");
        }

        // Create backup before saving
        $backupPath = $configPath . '.backup.' . date('YmdHis');
        copy($configPath, $backupPath);

        try {
            $cleanedProjectConfig = $this->removeNullOrEmptyValues($projectConfigDto->toArray());

            $yaml = Yaml::dump($cleanedProjectConfig, 4, 2);
            file_put_contents($configPath, $yaml);
        } catch (\Exception $e) {
            // Restore backup if save failed
            copy($backupPath, $configPath);
            unlink($backupPath);

            throw new \RuntimeException('Failed to save configuration: ' . $e->getMessage());
        }

        // Keep only last 5 backups
        $this->cleanupBackups($projectPath);
    }

    private function removeNullOrEmptyValues(array $data): array
    {
        foreach ($data as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                $data[$key] = $this->removeNullOrEmptyValues($value);

                if (empty($data[$key])) {
                    unset($data[$key]);
                }
            } elseif ($value === null || $value === '' || empty($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Get configuration file path
     */
    protected function getProjectConfigPath(string $projectPath): string
    {
        return $projectPath . '/' . self::CONFIG_FILE;
    }

    /**
     * Clean up old backup files, keeping only the last 5
     */
    protected function cleanupBackups(string $projectPath): void
    {
        $configPath  = $this->getProjectConfigPath($projectPath);
        $backupFiles = glob($configPath . '.backup.*');

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
