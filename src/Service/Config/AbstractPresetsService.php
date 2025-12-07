<?php

declare(strict_types=1);

namespace App\Service\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractPresetsService
{
    protected const CONFIG_DIR = 'config/app_defaults';

    protected array $config = [];

    public function __construct(
        protected readonly string $projectDir
    ) {
    }

    /**
     * Load a configuration file (cached)
     */
    protected function loadConfig(string $name): array
    {
        if (!isset($this->config[$name])) {
            $path = $this->projectDir . '/' . self::CONFIG_DIR . '/' . $name . '.yaml';
            $this->config[$name] = $this->loadYamlFile($path);
        }
        
        return $this->config[$name];
    }

    /**
     * Load a YAML file
     */
    protected function loadYamlFile(string $path): array
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Configuration file not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new \RuntimeException("Configuration file is not readable: {$path}");
        }

        try {
            $data = Yaml::parseFile($path);
            return is_array($data) ? $data : [];
        } catch (ParseException $e) {
            throw new \RuntimeException("Unable to parse YAML configuration: " . $e->getMessage());
        }
    }

    /**
     * Clear the configuration cache
     */
    public function clearCache(): void
    {
        $this->config = [];
    }
}