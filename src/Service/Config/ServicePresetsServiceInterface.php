<?php

declare(strict_types=1);

namespace App\Service\Config;

interface ServicePresetsServiceInterface
{
    public function getServiceTemplates(): array;
    public function getServiceTemplatesByCategory(string $category): array;
    public function getServiceTemplate(string $key): ?array;
    public function getServiceCategories(): array;
    public function getValidServiceCategories(): array;
}
