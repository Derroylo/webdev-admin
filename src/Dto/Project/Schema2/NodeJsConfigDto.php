<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class NodeJsConfigDto
{
    public string $version = '';

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->version = (string) ($data['version'] ?? '');

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'version' => $this->version,
        ];
    }
}