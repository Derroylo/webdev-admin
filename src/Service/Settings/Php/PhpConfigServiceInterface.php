<?php

declare(strict_types=1);

namespace App\Service\Settings\Php;

interface PhpConfigServiceInterface
{
    public function getPhpConfig(): array;

    public function updatePhpConfig(array $data): void;
}
