<?php

declare(strict_types=1);

namespace App\Service\Config;

interface TestPresetsServiceInterface
{
    public function getTestTemplates(): array;

    public function getTestTemplatesByCategory(string $category): array;

    public function getTestTemplate(string $key): ?array;

    public function getTestCategories(): array;
}
