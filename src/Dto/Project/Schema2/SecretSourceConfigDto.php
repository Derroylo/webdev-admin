<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class SecretSourceConfigDto
{
    public string $key = '';

    public string $group = '';

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->key = $data['key'] ?? '';
        $dto->group = $data['group'] ?? '';

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'group' => $this->group,
        ];
    }
}