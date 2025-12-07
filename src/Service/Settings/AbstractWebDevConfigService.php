<?php

declare(strict_types=1);

namespace App\Service\Settings;

use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractWebDevConfigService
{
    protected const CONFIG_FILE = '.devcontainer/webdev.yml';
    protected ?array $config = null;

    public function __construct(
        protected readonly ProjectSessionServiceInterface $projectSessionService
    ) {
    }

    /**
     * Get the current project directory from session
     */
    protected function getProjectDir(): string
    {
        $projectDir = $this->projectSessionService->getCurrentProjectPath();
        
        if ($projectDir === null) {
            throw new \RuntimeException('No project selected. Please select a project first.');
        }
        
        return $projectDir;
    }

    /**
     * Check if a project is currently selected
     */
    public function hasProjectSelected(): bool
    {
        return $this->projectSessionService->hasProjectSelected();
    }

    /**
     * Get the full configuration array
     */
    public function getConfig(): array
    {
        if ($this->config === null) {
            $this->loadConfig();
        }
        
        return $this->config;
    }

    /**
     * Load configuration from YAML file
     */
    protected function loadConfig(): void
    {
        $configPath = $this->getConfigPath();
        
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Configuration file not found: {$configPath}");
        }

        if (!is_readable($configPath)) {
            throw new \RuntimeException("Configuration file is not readable: {$configPath}");
        }

        try {
            $this->config = Yaml::parseFile($configPath);
        } catch (ParseException $e) {
            throw new \RuntimeException("Unable to parse YAML configuration: " . $e->getMessage());
        }
    }

    /**
     * Save configuration to YAML file
     */
    protected function saveConfig(): void
    {
        $configPath = $this->getConfigPath();
        
        if (!is_writable($configPath)) {
            throw new \RuntimeException("Configuration file is not writable: {$configPath}");
        }

        // Create backup before saving
        $backupPath = $configPath . '.backup.' . date('YmdHis');
        copy($configPath, $backupPath);

        try {
            $yaml = Yaml::dump($this->config, 4, 2);
            file_put_contents($configPath, $yaml);
        } catch (\Exception $e) {
            // Restore backup if save failed
            copy($backupPath, $configPath);
            unlink($backupPath);
            throw new \RuntimeException("Failed to save configuration: " . $e->getMessage());
        }

        // Keep only last 5 backups
        $this->cleanupBackups();
    }

    /**
     * Get configuration file path
     */
    protected function getConfigPath(): string
    {
        return $this->getProjectDir() . '/' . self::CONFIG_FILE;
    }

    /**
     * Clean up old backup files, keeping only the last 5
     */
    protected function cleanupBackups(): void
    {
        $configPath = $this->getConfigPath();
        $backupFiles = glob($configPath . '.backup.*');
        
        if (count($backupFiles) > 5) {
            // Sort by modification time, oldest first
            usort($backupFiles, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Remove oldest backups
            $toRemove = array_slice($backupFiles, 0, count($backupFiles) - 5);
            foreach ($toRemove as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Reload configuration from file (useful after external changes or project switch)
     */
    public function reloadConfig(): void
    {
        $this->config = null;
        $this->loadConfig();
    }

    /**
     * Clear cached configuration (useful when switching projects)
     */
    public function clearConfigCache(): void
    {
        $this->config = null;
    }
}
