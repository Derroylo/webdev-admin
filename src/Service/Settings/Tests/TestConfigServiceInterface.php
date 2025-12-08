<?php

declare(strict_types=1);

namespace App\Service\Settings\Tests;

interface TestConfigServiceInterface
{
    public function getTests(): array;

    public function getTest(string $key): ?array;

    public function createTest(string $key, array $data): void;

    public function updateTest(string $key, array $data): void;

    public function deleteTest(string $key): void;
}
