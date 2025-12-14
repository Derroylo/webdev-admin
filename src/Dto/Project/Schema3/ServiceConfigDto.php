<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema3;

class ServiceConfigDto
{
    public string $name = '';

    public string $category = '';

    public bool $active = false;

    public int $port = 0;

    public string $subDomain = '';
    

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'] ?? '';
        $dto->category = $data['category'] ?? '';
        $dto->active = $data['active'] ?? false;
        $dto->port = $data['port'] ?? 0;
        $dto->subDomain = $data['subDomain'] ?? '';
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'active' => $this->active,
            'port' => $this->port,
            'subDomain' => $this->subDomain,
        ];
    }
}