<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class SecretTargetConfigDto
{
    public string $file = '';

    public string $envVar = '';

    public array $expectedSecrets = [];

    public array $expectedVars = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->file = $data['file'] ?? '';
        $dto->envVar = $data['envVar'] ?? '';
        $dto->expectedSecrets = $data['expectedSecrets'] ?? [];
        $dto->expectedVars = $data['expectedVars'] ?? [];

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'file' => $this->file,
            'envVar' => $this->envVar,
            'expectedSecrets' => $this->expectedSecrets,
            'expectedVars' => $this->expectedVars,
        ];
    }
}