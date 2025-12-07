<?php

declare(strict_types=1);

namespace App\Service\Settings\Secrets;

interface SecretConfigServiceInterface
{
    public function getSecrets(): array;

    public function getSecret(string $key): ?array;

    public function createSecret(string $key, array $data): void;

    public function updateSecret(string $key, array $data): void;

    public function deleteSecret(string $key): void;
}