<?php

declare(strict_types=1);

namespace App\Service\Settings\Tasks;

interface TaskConfigServiceInterface
{
    public function getTasks(): array;

    public function getTask(string $key): ?array;

    public function createTask(string $key, array $data): void;

    public function updateTask(string $key, array $data): void;

    public function deleteTask(string $key): void;
}