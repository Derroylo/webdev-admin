<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class ServicesConfigDto
{
    /** @var array<string> */
    public array $active = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->active = $data['active'] ?? [];
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'active' => $this->active,
        ];
    }
}