<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class TaskConfigDto
{
    public string $name = '';

    public bool $onlyMain = false;

    /** @var array<string> */
    public array $init = [];

    /** @var array<string> */
    public array $create = [];

    /** @var array<string> */
    public array $start = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'] ?? '';
        $dto->onlyMain = $data['onlyMain'] ?? false;
        $dto->init = $data['init'] ?? [];
        $dto->create = $data['create'] ?? [];
        $dto->start = $data['start'] ?? [];
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'onlyMain' => $this->onlyMain,
            'init' => $this->init,
            'create' => $this->create,
            'start' => $this->start,
        ];
    }
}