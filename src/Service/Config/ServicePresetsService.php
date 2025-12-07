<?php

declare(strict_types=1);

namespace App\Service\Config;

class ServicePresetsService extends AbstractPresetsService implements ServicePresetsServiceInterface
{
     /**
     * Get all service templates from all categories
     */
    public function getServiceTemplates(): array
    {
        $allTemplates = [];
        $serviceDir = $this->projectDir . '/' . self::CONFIG_DIR . '/services';
        
        if (!is_dir($serviceDir)) {
            return [];
        }

        $files = glob($serviceDir . '/*.yaml');
        foreach ($files as $file) {
            $category = basename($file, '.yaml');
            $data = $this->loadYamlFile($file);
            
            if (isset($data['templates']) && is_array($data['templates'])) {
                foreach ($data['templates'] as $key => $template) {
                    $template['template_key'] = $key;
                    $template['template_category_file'] = $category;
                    $allTemplates[$key] = $template;
                }
            }
        }
        
        return $allTemplates;
    }

    /**
     * Get service templates by category file
     */
    public function getServiceTemplatesByCategory(string $category): array
    {
        $file = $this->projectDir . '/' . self::CONFIG_DIR . '/services/' . $category . '.yaml';
        
        if (!file_exists($file)) {
            return [];
        }

        $data = $this->loadYamlFile($file);
        return $data['templates'] ?? [];
    }

    /**
     * Get a specific service template
     */
    public function getServiceTemplate(string $key): ?array
    {
        $templates = $this->getServiceTemplates();
        return $templates[$key] ?? null;
    }

    /**
     * Get all service categories
     */
    public function getServiceCategories(): array
    {
        $categories = [];
        $serviceDir = $this->projectDir . '/' . self::CONFIG_DIR . '/services';
        
        if (!is_dir($serviceDir)) {
            return [];
        }

        $files = glob($serviceDir . '/*.yaml');
        foreach ($files as $file) {
            $category = basename($file, '.yaml');
            $categories[] = $category;
        }
        
        return $categories;
    }

    /**
     * Get valid service category names (extracted from templates)
     */
    public function getValidServiceCategories(): array
    {
        // Standard categories that services can belong to
        return [
            'proxy',
            'database',
            'mail',
            'cache',
            'search',
            'queue',
            'monitoring',
            'tools',
            'other'
        ];
    }
}