<?php

declare(strict_types=1);

namespace App\Service\Config;

interface PhpPresetsServiceInterface
{
    public function getPhpVersions(): array;

    public function getValidPhpVersions(): array;

    public function getDefaultPhpVersion(): string;

    public function getPhpSettings(): array;

    public function getPhpSetting(string $name): array;
}
