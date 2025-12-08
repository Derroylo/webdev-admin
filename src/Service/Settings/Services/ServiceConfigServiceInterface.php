<?php

declare(strict_types=1);

namespace App\Service\Settings\Services;

interface ServiceConfigServiceInterface
{
    public function getServices(): array;

    public function getService(string $key): ?array;

    public function createService(string $key, array $data): void;

    public function updateService(string $key, array $data): void;

    public function deleteService(string $key): void;

    public function toggleService(string $key, bool $active): void;
}
