<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Dto\Project\Schema2\ProjectConfigDto as Schema2ProjectConfigDto;
use App\Dto\Project\Schema3\ProjectConfigDto as Schema3ProjectConfigDto;
use Symfony\Component\Yaml\Yaml;

class ProjectConfigService implements ProjectConfigServiceInterface
{
    private const CONFIG_FILE = '.devcontainer/webdev.yml';
    private const DEVCONTAINER_FILE = '.devcontainer/devcontainer.json';

    /**
     * Get the project config for a given project path
     */
    public function getProjectConfig(string $projectPath): Schema2ProjectConfigDto | Schema3ProjectConfigDto | null
    {
        $configPath = $projectPath . '/' . self::CONFIG_FILE;

        if (!file_exists($configPath) || !is_readable($configPath)) {
            return null;
        }

        $config = Yaml::parseFile($configPath);

        if (!\is_array($config)) {
            return null;
        }

        if (!isset($config['schema']) || $config['schema'] === 2) {
            return Schema2ProjectConfigDto::fromArray($config);
        }

        if (isset($config['schema']) && $config['schema'] === 3) {
            return Schema3ProjectConfigDto::fromArray($config);
        }

        return null;
    }
}
