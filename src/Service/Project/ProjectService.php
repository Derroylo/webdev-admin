<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Dto\ProjectDto;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ProjectService implements ProjectServiceInterface
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY_PREFIX = 'projects_list_';
    private const CONFIG_FILE = '.devcontainer/webdev.yml';
    private const DEVCONTAINER_FILE = '.devcontainer/devcontainer.json';

    public function __construct(
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    /**
     * Get all projects from the given base path
     * Results are cached for performance
     *
     * @return ProjectDto[]
     */
    public function getAllProjects(string $basePath): array
    {
        $basePath = rtrim($basePath, '/');
        $cacheKey = self::CACHE_KEY_PREFIX . md5($basePath);

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            $cachedData = $cacheItem->get();
            
            // Check if base path still exists and hasn't changed
            if (is_dir($basePath) && isset($cachedData['lastScan'])) {
                $currentMtime = filemtime($basePath);
                if ($currentMtime <= $cachedData['lastScan']) {
                    // Convert cached arrays to DTOs
                    $projects = $cachedData['projects'] ?? [];
                    return array_map(fn(array $data) => ProjectDto::fromArray($data), $projects);
                }
            }
        }

        // Cache miss or expired, scan for projects
        return $this->scanProjects($basePath, $cacheKey);
    }

    /**
     * Force refresh projects (bypass cache)
     *
     * @return ProjectDto[]
     */
    public function refreshProjects(string $basePath): array
    {
        $basePath = rtrim($basePath, '/');
        $cacheKey = self::CACHE_KEY_PREFIX . md5($basePath);
        
        // Clear cache first
        $this->cache->deleteItem($cacheKey);
        
        // Scan and cache
        return $this->scanProjects($basePath, $cacheKey);
    }

    /**
     * Clear cache for projects
     */
    public function clearCache(?string $basePath = null): void
    {
        if ($basePath === null) {
            // Clear all project caches (find all cache items with our prefix)
            // Note: This is a simplified approach. For production, consider using cache tags
            $this->cache->clear();
        } else {
            $basePath = rtrim($basePath, '/');
            $cacheKey = self::CACHE_KEY_PREFIX . md5($basePath);
            $this->cache->deleteItem($cacheKey);
        }
    }

    /**
     * Scan directory for projects and cache results
     *
     * @return ProjectDto[]
     */
    private function scanProjects(string $basePath, string $cacheKey): array
    {
        if (!is_dir($basePath) || !is_readable($basePath)) {
            return [];
        }

        $projects = [];
        $items = @scandir($basePath);

        if ($items === false) {
            return [];
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $projectPath = $basePath . '/' . $item;

            // Skip if not a directory
            if (!is_dir($projectPath)) {
                continue;
            }

            // Check if this is a project (has .git or .devcontainer)
            if (!$this->isProjectDirectory($projectPath)) {
                continue;
            }

            // Extract project information
            $projectData = $this->extractProjectData($projectPath);
            if ($projectData !== null) {
                $projects[] = $projectData;
            }
        }

        // Sort by name
        usort($projects, fn(ProjectDto $a, ProjectDto $b) => strcasecmp($a->name, $b->name));

        // Cache the results (convert DTOs to arrays for storage)
        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->set([
            'projects' => array_map(fn(ProjectDto $dto) => $dto->toArray(), $projects),
            'lastScan' => time(),
            'basePath' => $basePath,
        ]);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        $this->cache->save($cacheItem);

        return $projects;
    }

    /**
     * Check if a directory is a project (has .git or .devcontainer)
     */
    private function isProjectDirectory(string $path): bool
    {
        return is_dir($path . '/.git') || is_dir($path . '/.devcontainer');
    }

    /**
     * Check if a project is webdev compatible (has .devcontainer/webdev.yml)
     */
    private function isWebdevCompatible(string $projectPath): bool
    {
        $configPath = $projectPath . '/' . self::CONFIG_FILE;
        return file_exists($configPath) && is_readable($configPath);
    }

    /**
     * Extract project data from a project directory
     */
    private function extractProjectData(string $projectPath): ?ProjectDto
    {
        $name = $this->extractProjectName($projectPath);
        $phpVersion = $this->extractPhpVersion($projectPath);
        $nodejsVersion = $this->extractNodejsVersion($projectPath);
        $isWebdevCompatible = $this->isWebdevCompatible($projectPath);

        $dto = new ProjectDto();
        $dto->name = $name;
        $dto->phpVersion = $phpVersion;
        $dto->nodejsVersion = $nodejsVersion;
        $dto->path = $projectPath;
        $dto->isWebdevCompatible = $isWebdevCompatible;
        
        // Extract tests only for compatible projects
        if ($isWebdevCompatible) {
            $dto->tests = $this->extractTests($projectPath);
        }

        return $dto;
    }

    /**
     * Extract project name from devcontainer.json or use folder name
     */
    private function extractProjectName(string $projectPath): string
    {
        $devcontainerPath = $projectPath . '/' . self::DEVCONTAINER_FILE;

        if (file_exists($devcontainerPath) && is_readable($devcontainerPath)) {
            try {
                $content = file_get_contents($devcontainerPath);
                if ($content !== false) {
                    // Remove comments from JSON before decoding (handling // and /* ... */)
                    $contentWithoutComments = preg_replace([
                        '/\/\/[^\n]*\n/',        // remove // comments
                        '/\/\*.*?\*\//s',        // remove /* */ comments
                    ], [
                        "\n",
                        "",
                    ], $content);
                    $json = json_decode($contentWithoutComments, true);
                    if (is_array($json) && isset($json['name']) && is_string($json['name'])) {
                        return $json['name'];
                    }
                }
            } catch (\Exception $e) {
                // Fall through to use folder name
            }
        }

        // Fallback to folder name
        return basename(rtrim($projectPath, '/'));
    }

    /**
     * Extract PHP version from webdev.yml
     */
    private function extractPhpVersion(string $projectPath): string
    {
        $configPath = $projectPath . '/' . self::CONFIG_FILE;

        if (!file_exists($configPath) || !is_readable($configPath)) {
            return '';
        }

        try {
            $config = Yaml::parseFile($configPath);
            if (is_array($config) && isset($config['php']['version'])) {
                return (string) $config['php']['version'];
            }
        } catch (ParseException $e) {
            // Return empty string on parse error
        } catch (\Exception $e) {
            // Return empty string on any other error
        }

        return '';
    }

    /**
     * Extract Node.js version from webdev.yml
     */
    private function extractNodejsVersion(string $projectPath): string
    {
        $configPath = $projectPath . '/' . self::CONFIG_FILE;

        if (!file_exists($configPath) || !is_readable($configPath)) {
            return '';
        }

        try {
            $config = Yaml::parseFile($configPath);
            if (is_array($config) && isset($config['nodejs']['version'])) {
                return (string) $config['nodejs']['version'];
            }
        } catch (ParseException $e) {
            // Return empty string on parse error
        } catch (\Exception $e) {
            // Return empty string on any other error
        }

        return '';
    }

    /**
     * Extract tests from webdev.yml
     * Returns an array of test key => test name
     *
     * @return array<string, string>
     */
    private function extractTests(string $projectPath): array
    {
        $configPath = $projectPath . '/' . self::CONFIG_FILE;

        if (!file_exists($configPath) || !is_readable($configPath)) {
            return [];
        }

        try {
            $config = Yaml::parseFile($configPath);
            if (!is_array($config) || !isset($config['tests']) || !is_array($config['tests'])) {
                return [];
            }

            $tests = [];
            foreach ($config['tests'] as $testKey => $testConfig) {
                if (is_array($testConfig) && isset($testConfig['name']) && is_string($testConfig['name'])) {
                    $tests[$testKey] = $testConfig['name'];
                }
            }

            return $tests;
        } catch (ParseException $e) {
            // Return empty array on parse error
        } catch (\Exception $e) {
            // Return empty array on any other error
        }

        return [];
    }
}

