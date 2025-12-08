<?php

declare(strict_types=1);

namespace App\Service\Config;

class TestPresetsService extends AbstractPresetsService implements TestPresetsServiceInterface
{
    /**
     * Get all test templates from all categories
     */
    public function getTestTemplates(): array
    {
        $allTemplates = [];
        $testDir      = $this->projectDir . '/' . self::CONFIG_DIR . '/tests';

        if (!is_dir($testDir)) {
            return [];
        }

        $files = glob($testDir . '/*.yaml');
        foreach ($files as $file) {
            $category = basename($file, '.yaml');
            $data     = $this->loadYamlFile($file);

            if (isset($data['templates']) && \is_array($data['templates'])) {
                foreach ($data['templates'] as $key => $template) {
                    $template['template_key']           = $key;
                    $template['template_category_file'] = $category;
                    $allTemplates[$key]                 = $template;
                }
            }
        }

        return $allTemplates;
    }

    /**
     * Get test templates by category file
     */
    public function getTestTemplatesByCategory(string $category): array
    {
        $file = $this->projectDir . '/' . self::CONFIG_DIR . '/tests/' . $category . '.yaml';

        if (!file_exists($file)) {
            return [];
        }

        $data = $this->loadYamlFile($file);

        return $data['templates'] ?? [];
    }

    /**
     * Get a specific test template
     */
    public function getTestTemplate(string $key): ?array
    {
        $templates = $this->getTestTemplates();

        return $templates[$key] ?? null;
    }

    /**
     * Get all test categories
     */
    public function getTestCategories(): array
    {
        $categories = [];
        $testDir    = $this->projectDir . '/' . self::CONFIG_DIR . '/tests';

        if (!is_dir($testDir)) {
            return [];
        }

        $files = glob($testDir . '/*.yaml');
        foreach ($files as $file) {
            $category     = basename($file, '.yaml');
            $categories[] = $category;
        }

        return $categories;
    }
}
