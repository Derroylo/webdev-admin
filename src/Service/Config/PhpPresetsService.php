<?php

declare(strict_types=1);

namespace App\Service\Config;

class PhpPresetsService extends AbstractPresetsService implements PhpPresetsServiceInterface
{
    /**
     * Get all PHP versions
     */
    public function getPhpVersions(): array
    {
        $versions = $this->loadConfig('versions');

        return $versions['php']['versions'] ?? [];
    }

    /**
     * Get valid PHP version strings (for validation)
     */
    public function getValidPhpVersions(): array
    {
        return array_column($this->getPhpVersions(), 'version');
    }

    /**
     * Get default PHP version
     */
    public function getDefaultPhpVersion(): string
    {
        $versions = $this->loadConfig('versions');

        return $versions['php']['default_version'] ?? '8.3';
    }

    public function getPhpSettings(): array
    {
        return $this->loadConfig('php_settings');
    }

    public function getPhpSetting(string $name): array
    {
        $settings = $this->getPhpSettings();

        return $settings[$name] ?? [];
    }

    /**
     * Get recommended settings grouped by application name
     *
     * @return array<string, array<string, string>> Array of [application_name => [setting_name => value]]
     */
    public function getRecommendedSettingsGroups(): array
    {
        $phpSettings = $this->getPhpSettings();
        $groups = [];

        foreach ($phpSettings as $category => $settings) {
            foreach ($settings as $setting) {
                $settingName = $setting['name'] ?? null;
                $recommended = $setting['recommended'] ?? [];

                if (!$settingName || empty($recommended)) {
                    continue;
                }

                foreach ($recommended as $recommendation) {
                    $appName = $recommendation['name'] ?? null;
                    $value = $recommendation['value'] ?? null;

                    if (!$appName || $value === null) {
                        continue;
                    }

                    if (!isset($groups[$appName])) {
                        $groups[$appName] = [];
                    }

                    $groups[$appName][$settingName] = $value;
                }
            }
        }

        return $groups;
    }
}
