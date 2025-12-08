<?php

declare(strict_types=1);

namespace App\Service\Config;

interface IdePresetsServiceInterface
{
    public function getIdeConfigs(): array;

    public function getIdeConfig(string $key): ?array;
}
